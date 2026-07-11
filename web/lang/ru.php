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

    // pages/awards.php
    // 'awards.title' is a best-effort adaptation (our RU source's
    // equivalent title is the shorter 'Награды', not a positional port).
    'awards.title'       => 'Награды',
    'awards.tab.daily'   => 'Награды за День',
    'awards.tab.global'  => 'Общие Награды',
    'awards.tab.ranks'   => 'Звания',
    'awards.tab.ribbons' => 'Медали',

    // pages/awards_daily.php
    // Our RU source restructured this whole title ("... за День" instead
    // of "... Awards") rather than translating word-for-word, so these
    // are adaptations, not a positional port. Note the still-untranslated
    // "Awards" word (see en.php comment) means ru currently renders a
    // mixed "Ежедневные Awards (date)" until that's split -- flagged, not
    // silently dropped.
    'awards_daily.period_daily'     => 'Ежедневные',
    'awards_daily.period_day_suffix' => 'дн.',
    'awards.no_winner'         => 'Нет Награжденных',

    // pages/awards_global.php
    'awards_global.title' => 'Общие Награды',

    // pages/awards_ranks.php (title reuses awards.tab.ranks)
    // 'Player List' -- our RU source left this untranslated too (§5.5 gap).
    // achieved_prefix is empty on purpose: our RU source restructures the
    // sentence as "<N> игроков заслужили" (number first, no lead-in word),
    // which the empty prefix + suffix keys reproduce without needing a
    // sprintf placeholder.
    'awards_ranks.achieved_prefix' => '',
    'awards_ranks.achieved_suffix' => ' игроков заслужили',

    // pages/awards_ribbons.php -- our RU source uses the same empty-prefix
    // restructuring here too (reuses awards_ranks.achieved_prefix).
    'awards_ribbons.achieved_suffix' => ' игроков заслужили',

    // pages/dailyawardinfo.php
    // breadcrumb_details/section_title both port from our source's
    // 'Награда Подробно'/'Награда подробно' -- note our RU source itself
    // has this same case inconsistency (capital vs lowercase "подробно"),
    // preserved as-is rather than "fixed" here. no_award_id is a fresh
    // translation (our RU source left this error message in English too).
    'dailyawardinfo.no_award_id'         => 'Не указан ID награды.',
    'dailyawardinfo.title'                => 'Награда Подробно',
    'dailyawardinfo.breadcrumb_awards'    => 'Награды',
    'dailyawardinfo.breadcrumb_details'   => 'Награда Подробно',
    'dailyawardinfo.col.day'              => 'День',
    'dailyawardinfo.col.count'            => 'Выполнено',
    'dailyawardinfo.section_title'        => 'Награда подробно',
    'dailyawardinfo.back_to'              => 'Вернуться к ',
    'dailyawardinfo.back_to_link'         => 'Наградам за День',

    // pages/rankinfo.php
    // no_rank_id/invalid_game are fresh translations (our RU source left
    // both error messages in English too). back_to_link is a separate key
    // from awards.tab.ranks despite the same English word: our RU source
    // uses the dative "Званиям" here (object of "к"), not the nominative
    // "Звания" the awards tab uses.
    'rankinfo.no_rank_id'    => 'Не указан ID звания.',
    'rankinfo.invalid_game'  => 'Неверная или не указана игра.',
    'rankinfo.title'         => 'Звание Подробно',
    'rankinfo.section_title' => 'Звание подробно',
    'rankinfo.col.skill'     => 'Очков',
    'rankinfo.back_to_link'  => 'Званиям',
];
