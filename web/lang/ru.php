<?php

/**
 * Russian locale catalog. Only translated keys need to be present here --
 * a key missing from this file falls back to web/lang/en.php (see
 * Service\LanguageService::get()). scripts/i18n/catalog-lint.php reports
 * such gaps as coverage warnings, not build errors.
 */

return [
    // common.* -- class_table.php / includes/functions.php (shared across pages)
    'common.col.rank'        => 'Ранг',
    'common.msg.error_heading' => 'ОШИБКА',
    'common.msg.empty'       => '---',
    'common.msg.undefined'   => 'Неизвестно',

    // common.nav.* -- pages/header.php (site chrome, shown on every page)
    'common.nav.contents'    => 'Статистика',
    'common.nav.search'      => 'Поиск',
    'common.nav.help'        => 'Помощь',
    'common.nav.servers'     => 'Серверы',
    'common.nav.chat'        => 'Чат',
    'common.nav.players'     => 'Игроки',
    'common.nav.clans'       => 'Кланы',
    'common.nav.countries'   => 'Страны',
    'common.nav.awards'      => 'Награды',
    'common.nav.actions'     => 'Действия',
    'common.nav.weapons'     => 'Оружие',
    'common.nav.maps'        => 'Карты',
    'common.nav.roles'       => 'Роли',
    'common.nav.bans'        => 'Баны',
    'header.banner_alt'      => 'Баннер',

    // common.col.* -- reusable table-column labels (first used by pages/players.php)
    'common.col.player'          => 'Игрок',
    'common.col.points'           => 'Очки',
    'common.col.activity'         => 'Активность',
    'common.col.connection_time'  => 'Время Игры',
    'common.col.kills'             => 'Убийств',
    'common.col.deaths'            => 'Смертей',
    'common.col.kpd'               => 'Уб:См',
    'common.col.headshots'         => 'Хедшотов',
    'common.col.hpk'                => 'ХШ:У',
    'common.col.accuracy'           => 'Точность',

    // pages/players.php
    'players.title'              => 'Ранги Игроков',
    'players.search.label'       => 'Найти игрока:',
    'players.search.submit'      => 'Искать',
    'players.rankview.label'     => 'Обзор Статистики',
    'players.rankview.submit'    => 'Посмотреть',
    'players.rankview.total'     => 'Общая Статистика',
    'players.rankview.lastweek'  => 'Последняя Неделя',
    'players.rankview.lastmonth' => 'Последний Месяц',
    'players.col.mmrank'         => 'Ранг',
    'players.minkills.pre'       => 'Показать Игроков с',
    'players.minkills.post'      => 'или более убийствами.',
    'players.minkills.submit'    => 'Показать',
    'players.nav.goto_label'     => 'Перейти к:',
    'players.nav.clan_rankings'  => 'Ранги Кланов',

    // common.col.* -- more reusable table-column labels (first used by search.php/search-class.php)
    'common.col.id'    => 'ID',
    'common.col.game'  => 'Игра',
    'common.col.tag'   => 'Тег',
    'common.col.name'  => 'Название',

    // pages/search.php + pages/search-class.php
    // Our RU source diverged from the fork here (dropped the "Player X"
    // plural phrasing, added a 'city' search type the fork doesn't have)
    // -- these are best-effort adaptations of the fork's exact English,
    // not a positional match. Flagged per FEAT-0027-PLAN §5.5.
    'search.type.player'          => 'Имя Игрока',
    'search.type.ip'               => 'IP-адрес',
    'search.type.clan'             => 'Клан',
    'search.form.title'            => 'Найти Игрока или Клан',
    'search.form.query_label'      => 'Искать:',
    'search.form.type_label'       => 'По:',
    'search.form.game_label'       => 'Игра:',
    'search.form.game_all'         => '(Все)',
    'search.form.submit'           => 'Искать',
    'search.results.title'         => 'Результаты Поиска',
    'search.results.count_prefix'  => 'Результаты поиска:',
    'search.results.count_suffix'  => 'совпадения',

    // pages/search-class.php: Search::$uniqueid_string(_plural), moved into
    // the constructor since __() can't be a property default (§4.4/PR review).
    // Our RU source left these untranslated ('Unique ID'/'Steam ID'), so
    // this is a fresh translation, not a port (plan §5.4 allows it).
    'search.uniqueid.singular'     => 'Уникальный ID',
    'search.uniqueid.plural'       => 'Уникальные ID',
    'search.uniqueid.ip_singular'  => 'IP-адрес',
    'search.uniqueid.ip_plural'    => 'IP-адрес',
];
