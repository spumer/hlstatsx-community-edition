<?php

    namespace Service;

    /**
     * Resolves the active visual theme (a css skin, optionally packaged as a
     * self-contained web/themes/<name>/ directory) and emits the byte-parity
     * stylesheet hrefs / icon path the legacy pages used to compute inline.
     *
     * Two theme forms are recognised:
     *  - legacy: a single web/styles/<name>.css skin (the 12 stock skins),
     *    optionally with a web/styles/<name>/ asset dir and a
     *    web/hlstatsimg/icons/<name>/ icon dir. No manifest.
     *  - package: a web/themes/<name>/ directory (theme.json manifest + css +
     *    assets + icons + chrome), self-contained.
     *
     * Name resolution is fail-safe (never throws): an unknown or malformed
     * name falls back to the configured default, mirroring i18n's degrade-to-
     * default behaviour. The whitelist below also closes the path-traversal /
     * reflected-XSS holes the old raw $_COOKIE['style'] / $_POST['stylesheet']
     * handling left open.
     */
    class ThemeService
    {
        private string $themesDir;
        private string $stylesDir;
        private string $imagePath;
        private string $default;

        private string $name;
        private bool $isPackage;

        /** @var array<string, mixed>|null */
        private ?array $manifest = null;

        public function __construct(string $themesDir, string $stylesDir, string $imagePath, string $default)
        {
            $this->themesDir = rtrim($themesDir, '/\\');
            $this->stylesDir = rtrim($stylesDir, '/\\');
            $this->imagePath = rtrim($imagePath, '/\\');
            $this->default = $default;

            $this->name = $default;
            $this->isPackage = false;
        }

        /**
         * Validate a raw style name (POST/cookie/option) and activate it.
         * Returns the resolved active theme name (never throws; falls back to
         * the configured default on empty/malformed/unknown input).
         */
        public function resolve(?string $raw): string
        {
            $name = $this->sanitize($raw);

            $this->name = $name;
            $this->isPackage = is_dir($this->themesDir . '/' . $name);
            $this->manifest = null;

            return $name;
        }

        public function name(): string
        {
            return $this->name;
        }

        public function isPackage(): bool
        {
            return $this->isPackage;
        }

        /**
         * Stylesheet hrefs for the page chrome, in link order. For the default
         * or a legacy skin this is byte-for-byte the old header.php:102-104
         * triplet: ['hlstats.css', 'styles/<name>.css', 'css/SqueezeBox.css'].
         */
        public function styleHrefs(): array
        {
            if (!$this->isPackage) {
                return ['hlstats.css', 'styles/' . $this->name, 'css/SqueezeBox.css'];
            }

            $manifest = $this->manifest();
            $dir = 'themes/' . $this->name;

            $base = (isset($manifest['base_css']) && $manifest['base_css'] !== '')
                ? $dir . '/' . $manifest['base_css']
                : 'hlstats.css';

            $cssList = (isset($manifest['css']) && is_array($manifest['css']) && $manifest['css'])
                ? $manifest['css']
                : ['theme.css'];

            $hrefs = [$base];
            foreach ($cssList as $css) {
                $hrefs[] = $dir . '/' . $css;
            }
            $hrefs[] = 'css/SqueezeBox.css';

            return $hrefs;
        }

        /**
         * Icon directory for nav/title images. Legacy: IMAGE_PATH/icons, or
         * IMAGE_PATH/icons/<style> when that per-skin dir exists (as the old
         * header.php:94-97 did). Package: the theme's own icons subdir.
         */
        public function iconPath(): string
        {
            $base = $this->imagePath . '/icons';

            if ($this->isPackage) {
                $sub = $this->manifest()['icons'] ?? 'icons';
                if (is_dir($this->themesDir . '/' . $this->name . '/' . $sub)) {
                    return 'themes/' . $this->name . '/' . $sub;
                }

                return $base;
            }

            $style = preg_replace('/\.css$/', '', $this->name);

            if ($style !== '' && is_dir($base . '/' . $style)) {
                return $base . '/' . $style;
            }

            return $base;
        }

        /**
         * Filesystem path of a package theme's chrome override for $part, or
         * null to fall back to the stock chrome file. Legacy skins never
         * override chrome. A package overrides a part when themes/<name>/chrome/
         * <part>.php exists; an optional manifest "chrome" whitelist can narrow
         * that (a part absent from the list is not overridden even if present).
         *
         * @param string $part 'header'|'footer'|'ingame_header'|'ingame_footer'
         */
        public function chromePath(string $part): ?string
        {
            if (!$this->isPackage) {
                return null;
            }

            if (!in_array($part, ['header', 'footer', 'ingame_header', 'ingame_footer'], true)) {
                return null;
            }

            $manifest = $this->manifest();
            if (isset($manifest['chrome']) && is_array($manifest['chrome'])
                && !in_array($part, $manifest['chrome'], true)) {
                return null;
            }

            $path = $this->themesDir . '/' . $this->name . '/chrome/' . $part . '.php';

            return is_file($path) ? $path : null;
        }

        /**
         * Themes offered by the user selector and the admin option: every
         * legacy skin (web/styles/*.css, in directory order) plus every
         * package (web/themes/<name>/), keyed by the value the resolver understands
         * (legacy 'foo.css', package 'foo') mapped to a display label.
         */
        public function listThemes(): array
        {
            $themes = [];

            if (is_dir($this->stylesDir)) {
                $d = dir($this->stylesDir);
                while (false !== ($e = $d->read())) {
                    if ($e === '.' || $e === '..') {
                        continue;
                    }
                    if (is_file($this->stylesDir . '/' . $e)) {
                        $themes[$e] = $this->legacyLabel($e);
                    }
                }
                $d->close();
            }

            if (is_dir($this->themesDir)) {
                $d = dir($this->themesDir);
                while (false !== ($e = $d->read())) {
                    if ($e === '.' || $e === '..') {
                        continue;
                    }
                    if (is_dir($this->themesDir . '/' . $e)) {
                        $themes[$e] = $this->packageLabel($e);
                    }
                }
                $d->close();
            }

            return $themes;
        }

        private function sanitize(?string $raw): string
        {
            if ($raw === null || $raw === '') {
                return $this->default;
            }

            $candidate = basename($raw);

            if ($candidate === $raw
                && preg_match('/^[A-Za-z0-9._-]+$/', $candidate)
                && $this->isWhitelisted($candidate)) {
                return $candidate;
            }

            error_log('theme: rejected style name ' . var_export($raw, true) . ", falling back to default '{$this->default}'");

            return $this->default;
        }

        private function isWhitelisted(string $name): bool
        {
            if (is_dir($this->themesDir . '/' . $name)) {
                return true;
            }

            return (bool) preg_match('/^[A-Za-z0-9._-]+\.css$/', $name)
                && is_file($this->stylesDir . '/' . $name);
        }

        private function manifest(): array
        {
            if ($this->manifest !== null) {
                return $this->manifest;
            }

            $this->manifest = [];

            if ($this->isPackage) {
                $path = $this->themesDir . '/' . $this->name . '/theme.json';
                if (is_file($path)) {
                    $decoded = json_decode((string) file_get_contents($path), true);
                    if (is_array($decoded)) {
                        $this->manifest = $decoded;
                    }
                }
            }

            return $this->manifest;
        }

        private function legacyLabel(string $file): string
        {
            return ucwords(strtolower(str_replace(['_', '.css'], [' ', ''], $file)));
        }

        private function packageLabel(string $name): string
        {
            $path = $this->themesDir . '/' . $name . '/theme.json';

            if (is_file($path)) {
                $decoded = json_decode((string) file_get_contents($path), true);
                if (is_array($decoded) && isset($decoded['name']) && $decoded['name'] !== '') {
                    return (string) $decoded['name'];
                }
            }

            return ucwords(str_replace(['_', '-'], ' ', $name));
        }
    }
