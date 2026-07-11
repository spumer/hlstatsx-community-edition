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
    // 'players.col.mmrank' -- no ru translation available: this TableColumn
    // (mmrank/elorank icon) doesn't exist in our RU source file at all, so
    // there's nothing to map (FEAT-0027-PLAN §5.5 "gap"); falls back to en.
    'players.minkills.pre'       => 'Показать Игроков с',
    'players.minkills.post'      => 'или более убийствами.',
    'players.minkills.submit'    => 'Показать',
    'players.nav.goto_label'     => 'Перейти к:',
    'players.nav.clan_rankings'  => 'Ранги Кланов',
];
