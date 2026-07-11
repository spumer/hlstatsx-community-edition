<?php

    namespace Service;

    /**
     * Resolves i18n string keys against an active-locale catalog with a
     * fallback chain: active locale -> fallback locale -> the key itself.
     *
     * Catalogs are plain PHP array files (web/lang/<locale>.php), loaded
     * lazily on first lookup and cached for the lifetime of the request.
     */
    class LanguageService
    {
        private string $langDir;
        private string $lang;
        private string $fallback;

        /** @var array<string, string>|null */
        private ?array $catalog = null;

        /** @var array<string, string>|null */
        private ?array $fallbackCatalog = null;

        public function __construct(string $langDir, string $lang, string $fallback = 'en')
        {
            $this->langDir = rtrim($langDir, '/\\');
            $this->lang = $lang;
            $this->fallback = $fallback;
        }

        public function setLanguage(string $lang): void
        {
            if ($lang === $this->lang) {
                return;
            }

            $this->lang = $lang;
            $this->catalog = null;
        }

        public function get(string $key): string
        {
            $this->loadCatalogs();

            if (array_key_exists($key, $this->catalog)) {
                return $this->catalog[$key];
            }

            if (array_key_exists($key, $this->fallbackCatalog)) {
                return $this->fallbackCatalog[$key];
            }

            error_log("i18n: key '{$key}' missing in both '{$this->lang}' and fallback '{$this->fallback}' catalogs");

            return $key;
        }

        public function has(string $key): bool
        {
            $this->loadCatalogs();

            return array_key_exists($key, $this->catalog);
        }

        public function hasFallback(string $key): bool
        {
            $this->loadCatalogs();

            return array_key_exists($key, $this->fallbackCatalog);
        }

        private function loadCatalogs(): void
        {
            if ($this->catalog === null) {
                $this->catalog = $this->loadFile($this->lang);
            }

            if ($this->fallbackCatalog === null) {
                $this->fallbackCatalog = $this->loadFile($this->fallback);
            }
        }

        private function loadFile(string $lang): array
        {
            $path = "{$this->langDir}/{$lang}.php";

            if (!is_file($path)) {
                return [];
            }

            $catalog = require $path;

            return is_array($catalog) ? $catalog : [];
        }
    }
