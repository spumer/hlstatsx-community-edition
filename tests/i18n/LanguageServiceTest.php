<?php

use Service\LanguageService;

$fixtures = __DIR__ . '/../fixtures/lang';

return [
    'LanguageService: resolves key from active locale (ru)' => function () use ($fixtures) {
        $svc = new LanguageService($fixtures, 'ru');
        hlx_assert_same('Привет', $svc->get('sample.hello'), 'ru value should win over en');
    },

    'LanguageService: falls back to en when ru misses the key' => function () use ($fixtures) {
        $svc = new LanguageService($fixtures, 'ru');
        hlx_assert_same('English only', $svc->get('sample.only_en'), 'must fall back to en catalog');
    },

    'LanguageService: falls back to the key itself when missing everywhere' => function () use ($fixtures) {
        $svc = new LanguageService($fixtures, 'ru');
        hlx_assert_same('nowhere.to.be.found', $svc->get('nowhere.to.be.found'), 'unknown key must return itself (fail-safe)');
    },

    'LanguageService: default locale en resolves straight from en.php' => function () use ($fixtures) {
        $svc = new LanguageService($fixtures, 'en');
        hlx_assert_same('Hello', $svc->get('sample.hello'), 'en is both active and fallback here');
    },

    'LanguageService: missing active-locale file degrades to en only (parity)' => function () use ($fixtures) {
        $svc = new LanguageService($fixtures, 'de'); // no de.php fixture on purpose
        hlx_assert_same('Hello', $svc->get('sample.hello'), 'no active catalog -> behaves like en');
    },

    'LanguageService: has() reflects active locale only' => function () use ($fixtures) {
        $svc = new LanguageService($fixtures, 'ru');
        hlx_assert_true($svc->has('sample.hello'), 'ru has sample.hello');
        hlx_assert_false($svc->has('sample.only_en'), 'ru does not have sample.only_en');
    },

    'LanguageService: hasFallback() reflects en catalog' => function () use ($fixtures) {
        $svc = new LanguageService($fixtures, 'ru');
        hlx_assert_true($svc->hasFallback('sample.only_en'), 'en has sample.only_en');
        hlx_assert_false($svc->hasFallback('nowhere.to.be.found'), 'en does not have unknown key');
    },

    'LanguageService: setLanguage() switches active locale after construction' => function () use ($fixtures) {
        $svc = new LanguageService($fixtures, 'en');
        hlx_assert_same('Hello', $svc->get('sample.hello'), 'starts as en');

        $svc->setLanguage('ru');
        hlx_assert_same('Привет', $svc->get('sample.hello'), 'switches to ru after setLanguage');
    },
];
