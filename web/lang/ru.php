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

    // common.err.* -- see en.php comment above these same keys
    'common.err.no_such_game'         => 'Нет такой игры \'%s\'.',
    'common.err.no_such_player'       => 'Нет такого игрока \'%s\'.',
    'common.err.no_such_clan'         => 'Нет такого клана \'%s\'.',
    'common.err.no_such_countryclan'  => 'Нет такой страны \'%s\'.',

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

    // pages/ribboninfo.php
    // no_ribbon_id is a fresh translation (our RU source left it in
    // English too). col.daily_awards is a best-effort adaptation (our
    // source simplified to 'Всего' = "Total"). times_suffix is a fresh
    // translation (our RU source also left " times" untranslated).
    // back_to_link is separate from awards.tab.ribbons for the same
    // dative-case reason as rankinfo.back_to_link.
    'ribboninfo.no_ribbon_id'      => 'Не указан ID медали.',
    'ribboninfo.title'             => 'Медаль Подробно',
    'ribboninfo.section_title'     => 'Медаль подробно',
    'ribboninfo.col.daily_awards'  => 'Всего',
    'ribboninfo.times_suffix'      => ' раз',
    'ribboninfo.back_to_link'      => 'Медалям',

    // pages/clans.php
    // minmembers.pre/post/total_suffix are best-effort adaptations: our
    // RU source restructures the whole sentence ("N и более участниками.
    // Всего M Кланов" vs the fork's "N or more members from a total of M
    // clans") rather than translating word-for-word.
    'clans.col.clan'             => 'Клан',
    'clans.col.avg_points'       => 'Очки (ср.)',
    'clans.col.members'          => 'Участников',
    'clans.search.label'         => 'Найти Клан:',
    'clans.minmembers.pre'       => 'Показать Кланы с',
    'clans.minmembers.post'      => 'или более участников из ',
    'clans.minmembers.total_suffix' => ' кланов',

    // pages/countryclans.php -- again a restructured sentence ("N и более
    // участников. Всего M Стран"), and a page-appropriate 'Страны'
    // translation distinct from clans.php's 'Кланы' despite the fork
    // using byte-identical English for both pre-fragments.
    'countryclans.title'              => 'Ранги Стран',
    'countryclans.col.country'        => 'Страна',
    'countryclans.minmembers.pre'          => 'Показать Страны с',
    'countryclans.minmembers.post'         => 'и более участников. Всего ',
    'countryclans.minmembers.total_suffix' => ' Стран',

    // pages/countryclansinfo.php
    // no_country_id is a fresh translation (our RU source left it in
    // English too). col.name is a best-effort adaptation: our source
    // uses 'Игрок' here (as everywhere else it labels a player-name
    // column), even though the fork's English says "Name" not "Player".
    'countryclansinfo.no_country_id'          => 'Не указан ID страны.',
    'countryclansinfo.title'                  => 'Страна Подробно',
    'countryclansinfo.section_title'          => 'Информация о стране',
    'countryclansinfo.stats_summary'          => 'Сводная статистика',
    'countryclansinfo.row.country'            => 'Страна:',
    'countryclansinfo.row.activity'           => 'Активность:',
    'countryclansinfo.row.members'            => 'Участников:',
    'countryclansinfo.active_members'         => 'активных участников',
    'countryclansinfo.row.total_kills'        => 'Всего убийств:',
    'countryclansinfo.row.total_deaths'       => 'Всего смертей:',
    'countryclansinfo.row.avg_kills'          => 'Убийств (среднее):',
    'countryclansinfo.row.kills_per_death'    => 'Убийств / Смертей:',
    'countryclansinfo.row.kills_per_minute'   => 'Убийств в минуту:',
    'countryclansinfo.row.avg_member_points'  => 'Очки участников (среднее):',
    'countryclansinfo.row.avg_connection_time' => 'Среднее время игры:',
    'countryclansinfo.row.total_connection_time' => 'Общее время игры:',
    'countryclansinfo.col.name'               => 'Игрок',
    'countryclansinfo.col.time'                => 'Время',
    'countryclansinfo.col.clan_kills'          => 'Клановых убийств',
    'countryclansinfo.col.kpd'                  => 'Уб:См',
    'countryclansinfo.members_title'          => 'Участники',

    // pages/contents.php
    // clan_icon_alt/player_icon_alt use our RU source's simpler icon-alt
    // translations ('Кланы'/'Игроки'), not a literal port of the fork's
    // more verbose "Clan Rankings"/"Player Rankings" alt text.
    'contents.games_title'            => 'Игры',
    'contents.col.game'                => 'Игра',
    'contents.col.top_player'          => 'Лучший Игрок',
    'contents.col.top_clan'            => 'Лучший Клан',
    'contents.clan_icon_alt'           => 'Кланы',
    'contents.player_icon_alt'         => 'Игроки',
    'contents.general_stats_title'    => 'Общая Статистика',
    'contents.last_kill_label'        => 'Последнее Убийство',
    'contents.stats_disclaimer.pre'   => 'Вся статистика ведется в реальном времени. Данные об истории событий хранятся ',
    'contents.stats_disclaimer.post'  => ' дней.',

    // pages/roles.php
    'roles.title'         => 'Роли',
    'roles.col.role'      => 'Роль',
    'roles.col.picked'    => 'Выбрана',
    'roles.col.ratio'     => 'Соотношение',
    'roles.stats.pre'     => 'Всего ',
    'roles.stats.mid'     => ' убийств и ',
    'roles.stats.post'    => ' смертей',

    // pages/rolesinfo.php
    // no_role_id is a fresh translation (our RU source left it in
    // English too). breadcrumb_details/section_title preserve our RU
    // source's own case inconsistency ('Роль Подробно' vs 'Роль
    // подробно'), same pattern as dailyawardinfo/rankinfo/ribboninfo.
    // headshots_with/headshots_word are approximate: our RU source
    // restructures this whole clause ("из них N хедшотами" -- "of them,
    // N by headshots") rather than a literal "with N headshots".
    // back_to_link uses the dative 'Ролям' (object of "к"), distinct
    // from breadcrumb_roles' nominative 'Роли'.
    'rolesinfo.no_role_id'         => 'Не указан ID роли.',
    'rolesinfo.title'              => 'Роль Подробно',
    'rolesinfo.breadcrumb_roles'   => 'Роли',
    'rolesinfo.breadcrumb_details' => 'Роль Подробно',
    'rolesinfo.section_title'      => 'Роль подробно',
    'rolesinfo.col.kills_suffix'   => ' убил',
    'rolesinfo.stats.pre'          => '',
    'rolesinfo.stats.mid'          => ' убийств сделал ',
    'rolesinfo.headshots_with'     => 'из них',
    'rolesinfo.headshots_word'     => 'хедшотами',
    'rolesinfo.stats.days_pre'     => '(за последние ',
    'rolesinfo.stats.days_post'    => ' дней)',
    'rolesinfo.back_to_link'       => 'Ролям',

    // pages/maps.php
    // 'HeatMap' -- our RU source left this untranslated too (§5.5 gap).
    // maps.stats.mid is a separate key from roles.stats.mid despite
    // byte-identical English ('kills with' in both fork files): our RU
    // source phrases the maps-page sentence as "убийств, из них" (kills,
    // of which) rather than roles.php's "убийств и" (kills and).
    'maps.title'        => 'Карты',
    'maps.col.map'      => 'Карта',
    'maps.stats.mid'    => ' убийств, из них ',
    'maps.stats.post'   => ' хедшотов',

    // pages/claninfo.php
    'claninfo.no_clan_id'         => 'Не указан ID клана.',
    'claninfo.title'              => 'Клан Подробно',
    'claninfo.tab.general'        => 'Общее',
    'claninfo.tab.teams_actions'  => 'Командные',
    'claninfo.marked_note.pre'    => 'Отмеченные "*" пункты сгенерированы за последние ',
    'claninfo.marked_note.post'   => ' дней.',
    'claninfo.admin_options_label' => 'Настройки Админа: ',

    // pages/claninfo_general.php
    'claninfo_general.section_title'          => 'Информация о клане',
    'claninfo_general.label.clan'              => 'Клан:',
    'claninfo_general.label.homepage'          => 'Домашняя страница:',
    'claninfo_general.homepage_not_specified'  => '(Не указано)',
    'claninfo_general.label.favorite_server'   => 'Любимый сервер:*',
    'claninfo_general.label.favorite_map'      => 'Любимая карта:*',
    'claninfo_general.label.favorite_weapon'   => 'Любимое оружие:*',
    'claninfo_general.col.player_locations'    => 'Местоположение игроков',

    // pages/claninfo_actions.php
    'claninfo_actions.col.action'             => 'Действие',
    'claninfo_actions.col.achieved'           => 'Выполнено',
    'claninfo_actions.col.points_bonus'       => 'Вознаграждение',
    'claninfo_actions.title.player_actions'   => 'Действия игроков *',
    'claninfo_actions.col.times_victimized'   => 'Стали Жертвами, раз(а)',
    'claninfo_actions.title.victims'          => 'Жертвы взаимодействий Игрок-Игрок *',

    // pages/claninfo_teams.php ('Percentage of Times' is a semantic
    // adaptation, not a literal translation -- zozo labels this column
    // the same as the Role Ratio column below)
    'claninfo_teams.col.team'                 => 'Команда',
    'claninfo_teams.col.joined'               => 'Играли',
    'claninfo_teams.col.percentage_of_times'  => 'Соотношение',
    'claninfo_teams.title.team_selection'     => 'Выбор команды *',
    'claninfo_teams.title.role_selection'     => 'Выбор роли *',

    // pages/claninfo_weapons.php
    'claninfo_weapons.col.weapon'                  => 'Оружие',
    'claninfo_weapons.col.points_modifier'         => 'Коэффициент',
    'claninfo_weapons.col.percentage_of_kills'     => 'Соотношение',
    'claninfo_weapons.col.percentage_of_headshots' => 'Соотношение',
    'claninfo_weapons.col.hpk'                      => 'ХШ:У',
    'claninfo_weapons.title.weapon_usage'          => 'Использование оружия *',
    'claninfo_weapons.col.shots'                    => 'Выстрелов',
    'claninfo_weapons.col.hits'                     => 'Попаданий',
    'claninfo_weapons.col.damage'                   => 'Урон',
    'claninfo_weapons.col.kills_per_death'         => 'Уб:См',
    'claninfo_weapons.col.damage_per_hit'          => 'Урон/Попаданий',
    'claninfo_weapons.col.shots_per_kill'          => 'Выстр./Уб.',
    'claninfo_weapons.title.weapon_stats'          => 'Статистика оружия *',
    'claninfo_weapons.col.left'                     => 'Влево',
    'claninfo_weapons.col.middle'                   => 'Центр',
    'claninfo_weapons.col.right'                    => 'Вправо',
    'claninfo_weapons.title.weapon_targets'        => 'Цели оружия *',
    'claninfo_weapons.col.targets_header'          => 'Цели оружия',
    'claninfo_weapons.link.show_total_stats'       => 'Показать общую статистику попаданий',

    // pages/claninfo_mapperformance.php
    'claninfo_mapperformance.col.map_name' => 'Карта',
    'claninfo_mapperformance.title'        => 'Статистика карт *',

    // pages/chat.php
    'chat.title'                  => 'Чат',
    'chat.default.all_servers'    => '(Все Серверы)',
    'chat.default.unknown_server' => '(Неизвестный сервер)',
    'chat.col.date'                => 'Дата',
    'chat.col.message'             => 'Сообщение',
    'chat.col.server'              => 'Сервер',
    'chat.label.show_chat_from'   => 'Показать Чат',
    'chat.option.all_servers'     => 'Все Серверы',
    'chat.label.filter'            => 'Фильтр:',
    'chat.btn.view'                 => 'Показать',
    'chat.btn.clear'                => 'Очистить',
    'chat.msg.delay_pre'           => '*Сообщения задерживаются на ',
    'chat.msg.delay_post'          => ' минут для предотвращения отслеживания в реальном времени.',

    // pages/chathistory.php
    'chathistory.no_player_id'          => 'ID игрока не указан или некорректен.',
    'chathistory.title'                  => 'История Чата',
    'chathistory.nav.player_details'    => 'Игрок Подробно',
    'chathistory.suffix.statistics'     => ' - статистика',

    // pages/actions.php
    'actions.title'      => 'Действия',
    'actions.col.earned' => 'Выполнено',
    'actions.col.reward' => 'Вознаграждение',
    'actions.stats.pre'  => 'Всего ',
    'actions.stats.post' => ' выполненных действий',

    // pages/actioninfo.php
    'actioninfo.no_action_id'          => 'Не указан ID действия.',
    'actioninfo.invalid_game'          => 'Некорректный ID игры или игра не указана.',
    'actioninfo.title'                  => 'Действие Подробно',
    'actioninfo.col.skill_bonus_total' => 'Получено вознаграждения',
    'actioninfo.stats.mid1'            => ' всего ',
    'actioninfo.stats.mid2'            => ' раз(а) (за последние  ',
    'actioninfo.stats.post'            => ' дней)',
    'actioninfo.back_to.label'         => 'Вернуться к ',
    'actioninfo.back_to.action_stats'  => 'Действиям',
    'actioninfo.title.victims'         => 'Жертвы',
    'actioninfo.victims.label'         => 'Жертвы ',
    'actioninfo.victims.days_pre'      => ' (за последние ',

    // pages/weapons.php
    'weapons.title'        => 'Оружие',
    'weapons.col.modifier' => 'Коэффициент',
    'weapons.col.kills'    => 'Убито',

    // pages/weaponinfo.php
    'weaponinfo.no_weapon_id'        => 'Не указан ID оружия.',
    'weaponinfo.title'                => 'Оружие Подробно',
    'weaponinfo.col.kills_suffix'    => ' убито',
    'weaponinfo.stats.mid2'          => ' хедшотов (за последние ',
    'weaponinfo.back_to.weapon_stats' => 'Оружию',

    // pages/mapinfo.php
    'mapinfo.no_map'              => 'Карта не указана.',
    'mapinfo.title'                => 'Карта Подробно',
    'mapinfo.stats.mid'            => ' убийств (за последние ',
    'mapinfo.back_to.map_stats'   => 'Картам',

    // pages/servers.php
    'servers.invalid_server_id' => 'Указан некорректный ID сервера.',
    'servers.title.live_view'    => 'Сервер в реальном времени',
    'servers.title.load_history' => 'История нагрузки сервера',
    'servers.col.server'          => 'Сервер',
    'servers.col.address'         => 'Адрес',
    'servers.col.played'          => 'Время',
    'servers.col.players'         => 'Игроки',
    'servers.col.headshots'       => 'Хедшоты',
    'servers.period.24h'          => '24h Часа',
    'servers.period.last_week'    => 'Последняя Неделя',
    'servers.period.last_month'   => 'Последний Месяц',
    'servers.period.last_year'    => 'Последний Год',

    // pages/livestats.php
    'livestats.col.kills'      => 'Уб:См',
    'livestats.col.hs'          => 'ХШ',
    'livestats.col.acc'         => 'Точность',
    'livestats.col.lat'         => 'Пинг',
    'livestats.col.skill'       => 'Очки',
    'livestats.unknown_team'   => 'Неизвестная команда',
    'livestats.msg.unknown'    => 'Неизвестно',
    'livestats.suffix.wins'    => ' побед)',
    'livestats.msg.no_players' => 'Нет игроков',

    // pages/playerinfo_servers.php
    'playerinfo_servers.col.percentage_of_headshots' => 'Процент Хедшотов',
    'playerinfo_servers.title' => 'Активность на Серверах *',

    // pages/playerinfo_aliases.php
    'playerinfo_aliases.col.name'      => 'Ник',
    'playerinfo_aliases.col.last_use'  => 'Послед. использование',
    'playerinfo_aliases.col.kills'     => 'Убийства',
    'playerinfo_aliases.col.deaths'    => 'Смерти',
    'playerinfo_aliases.col.suicides'  => 'Самоубийств',
    'playerinfo_aliases.title'          => 'Ники Игрока',

    // pages/playerinfo_playeractions.php
    'playerinfo_playeractions.col.accumulated_points' => 'Получено очков',
    'playerinfo_playeractions.title'                  => 'Действия игрока *',
    'playerinfo_playeractions.col.earned_against'     => 'Стал Жертвой, раз(а)',

    // pages/playerawards.php
    'playerawards.no_player_id'          => 'Не указан ID игрока.',
    'playerawards.title'                  => 'История Наград',
    'playerawards.no_award_id_bug'       => 'Не указан ID клана.',
    'playerawards.col.count'              => 'Кол-во',
    'playerawards.col.date'               => 'Дата',
    'playerawards.col.date_last_earned'  => 'Дата получения',
    'playerawards.col.description'        => 'Описание',
    'playerawards.section_title'          => 'История наград игрока',

    // pages/playersessions.php
    'playersessions.title'                => 'История Игровых Сессий',
    'playersessions.col.skill_change'    => 'Изменение Навыка',
    'playersessions.col.hs'               => 'Хедшотов',
    'playersessions.col.tks'              => 'Тимкилл',
    'playersessions.col.kill_streak'     => 'Серия убийств',
    'playersessions.section_title'        => 'История сессий игрока',
    'playersessions.footer_note.pre'      => 'Отмеченный "*" пункты сгенерированы за последние ',

    // pages/playerinfo_killstats.php
    'playerinfo_killstats.col.victim'         => 'Жертва',
    'playerinfo_killstats.title'               => 'Статистика убийств игрока *',
    'playerinfo_killstats.label.show_victims' => 'Показать жертв, которых убил этот игрок',
    'playerinfo_killstats.label.or_more_times' => 'раз и более',

    // pages/playerinfo.php ("Banned"/"In good standing" are fresh-
    // translated: zozo's playerinfo_general.php has a structurally
    // different feature (VAC/trade-ban status) in this position, not a
    // direct equivalent of the fork's hideranking-based status, so its
    // wording ("Испорчена"/"В порядке") wasn't reused directly except
    // where the "good" state phrase is generically applicable.)
    'playerinfo.status.banned'          => '<span style="color:red;font-weight:bold;">Забанен</span>',
    'playerinfo.status.good_standing'  => '<span style="color:green;font-weight:bold;">В порядке</span>',
    'playerinfo.tab.maps_servers'       => 'Карты/Серверы',
    'playerinfo.tab.killstats'          => 'Убийства',

    // pages/playerinfo_teams.php
    'playerinfo_teams.col.joined' => 'Играл',

    // pages/playerhistory.php
    'playerhistory.title'          => 'История Событий',
    'playerhistory.col.type'        => 'Тип',
    'playerhistory.title_bar.pre'  => 'История событий игрока (за последние ',

    // pages/playerinfo_weapons.php
    'playerinfo_weapons.title.weapon_stats' => 'Статистика Оружия *',
    'playerinfo_weapons.col.hits_flash'     => 'Попадания',
    'playerinfo_weapons.col.targets_header' => 'Попадания',

    // pages/playerinfo_general.php
    'playerinfo_general.title'                => 'Информация об игроке',
    'playerinfo_general.col.player_profile'   => 'Профиль игрока',
    'playerinfo_general.label.location'       => 'Местоположение: ',
    'playerinfo_general.location_unknown'     => 'Местоположение: (Неизвестно)',
    'playerinfo_general.label.status'         => 'Статус:',
    'playerinfo_general.link.add_friend'      => 'Добавить в Друзья в Steam',
    'playerinfo_general.label.member_of_clan' => 'Участник Клана:',
    'playerinfo_general.no_clan'              => '(Без Клана)',
    'playerinfo_general.label.real_name'      => 'Настоящее имя:',
    'playerinfo_general.link.not_specified_suffix' => '?mode=help#set"><em>Не Указано</em></a>)',
    'playerinfo_general.msg.not_specified'    => 'Не Указано',
    'playerinfo_general.label.email'          => 'E-mail адрес:',
    'playerinfo_general.label.mm_rank'        => 'ММ Ранг:',
    'playerinfo_general.label.last_connect'   => 'Последнее соединение:',
    'playerinfo_general.msg.unknown_paren'    => '(Неизвестно)',
    'playerinfo_general.label.avg_ping'       => 'Средний пинг:*',
    'playerinfo_general.row.points'                 => 'Очков:',
    'playerinfo_general.row.rank'                   => 'Ранг:',
    'playerinfo_general.rank.hidden'                => 'Скрытый',
    'playerinfo_general.rank.excluded'              => '<span style="color:red;">Исключен из подсчета рангов</span>',
    'playerinfo_general.rank.not_active'            => 'Не активен',
    'playerinfo_general.row.kills_per_minute'       => 'Убийств в минуту:',
    'playerinfo_general.row.kills_per_death'        => 'Убийств/Смертей:',
    'playerinfo_general.row.headshots_per_kill'     => 'Хедшотов/Убийств:',
    'playerinfo_general.row.shots_per_kill'         => 'Выстрелов/Убийств:',
    'playerinfo_general.row.weapon_accuracy'        => 'Точность стрельбы:',
    'playerinfo_general.row.headshots'              => 'Хедшотов:',
    'playerinfo_general.row.kills'                   => 'Убийств:',
    'playerinfo_general.row.deaths'                  => 'Смертей:',
    'playerinfo_general.row.longest_kill_streak'    => 'Самая длительная серия Убийств:',
    'playerinfo_general.row.longest_death_streak'   => 'Самая длительная серия Смертей:',
    'playerinfo_general.row.suicides'                => 'Самоубийств:',
    'playerinfo_general.row.teammate_kills'          => 'Убийств членов команды:',
    'playerinfo_general.col.player_trend'     => 'График игрока',
    'playerinfo_general.col.forum_signature'  => 'Подпись для Форума',
    'playerinfo_general.bbcode.phpbb'         => 'BB-код 1 (phpBB, SMF)',
    'playerinfo_general.bbcode.ipb'           => 'BB-код 2 (IPB)',
    'playerinfo_general.bbcode.direct_image'  => 'Прямая ссылка',
    'playerinfo_general.title.ranks'          => 'Звания',
    'playerinfo_general.label.current_rank'   => 'Текущее звание:',
    'playerinfo_general.label.kills_needed'   => 'Необходимо убийств:',
    'playerinfo_general.col.rank_history'     => 'История званий',
    'playerinfo_general.title.awards'         => 'Награды (Наведите на картинку, чтобы увидеть название)',
    'playerinfo_general.col.ribbons'          => 'Медали',
    'playerinfo_general.col.global_awards'    => 'Общие награды',

    // pages/footer.php
    'footer.msg.nojs'          => 'Вы просматриваете базовую версию этой страницы. Включите JavaScript и перезагрузите страницу для полного функционала.',
    'footer.label.generated_by' => 'Сгенерировано в реальном времени с помощью ',
    'footer.msg.copyright'    => 'Все изображения защищены авторским правом их владельцев.',
    'footer.link.admin'        => 'Admin',
    'footer.link.logout'       => 'Logout',

    // pages/bans.php
    'bans.title'                        => 'Читеры и Забаненные Игроки',
    'bans.col.ban_date'                  => 'Дата бана',
    'bans.label.find_a_player'          => 'Найти игрока:',
    'bans.btn.search'                    => 'Поиск',
    'bans.label.show_only_players_with' => 'Показывать только игроков с',
    'bans.stats.mid'                     => 'или более убийств из общего числа ',
    'bans.stats.post'                    => ' забаненных игроков',
    'bans.btn.apply'                     => 'Применить',

    // pages/game.php
    'game.title.participating_servers' => 'Действующие Серверы',
    'game.col.players'                  => 'Игроков',
    'game.range.24h'                     => '24 Часа',
    'game.range.last_week'               => 'Неделя',
    'game.range.last_month'              => 'Месяц',
    'game.range.last_year'               => 'Год',
    'game.msg.unknown_country'          => 'Неизвестная страна',
    'game.msg.no_award_winner'          => '&nbsp;&nbsp; <em>Нет Награжденных</em>',

    // pages/help.php
    'help.title'                => 'Помощь',
    'help.title.questions'      => 'Вопросы',
    'help.q.players'            => 'По какому критерию отслеживаются игроки? Почему мое имя отображается несколько раз?',
    'help.q.points'              => 'Как начисляются очки?',
    'help.q.weaponmods'          => 'Что такое коэффициенты оружия?',
    'help.q.set'                 => 'Как я могу добавить в профиль свое настоящее имя, e-mail и домашнюю страничку?',
    'help.q.hideranking'         => 'Меня смущает мой ранг. Как я могу его скрыть?',
    'help.title.answers'        => 'Ответы',
    'help.text.name_tracking'   => 'Игрок может иметь более одного ника. На странице статистики игроков игроки отображаются под последним ником, используемым им в игре. Если Вы кликните на ник игрока, на его персональной странице будет отображен список всех других имен, используемых данным игроком, если такие имеются в секции "История ников игрока". Если игрок использует один ник, указанная секция будет скрыта.',
    'help.text.name_listed_pre'  => 'Ваш ник может отображаться несколько раз, если кто либо-еще (с другим ',
    'help.text.name_listed_post' => ') использует такой же ник.',
    'help.text.search_pre'       => 'Вы можете использовать ',
    'help.link.search'           => 'ПОИСК',
    'help.text.search_mid'       => ', чтобы найти игрока по имени или ',
    'help.text.search_post'      => '.',
    'help.text.points_gain'      => 'У нового игрока по умолчанию 1000 очков. При каждом убийстве вы получаете определенное количество очков, зависящих от А) ранга жертвы и Б) оружия, которое вы использовали. Если вы убили кого-либо выше Вас по рангу, Вы получите больше очков за его убийство, как если вы убьете кого-либо ниже Вас по рангу. Таким образом, убийство нубов не будет для Вас столь полезным, как убийство "Отцов". Также если вы убьете кого-либо с помощью рукопашного оружия, вы получите больше очков, чем за убийство, к примеру, с помощью автомата.',
    'help.text.points_lose'      => 'Когда убивают Вас, Вы теряете некоторое количиество очков опыта, которое также зависит от ранга вашего убивца и оружия, которое он использовал для столь коварной цели. (Вы потеряете меньше очков в случае, если вас убьет "Папа" из автомата, чем если вы будете убиты нубом с помощью рукопашного оружия. Это делает довольно легким продвижение по лестнице ранга, но удержаться на его вершине является весьма проблематичной задачей.',
    'help.text.equations_intro'  => 'Расчет очков выглядит следующим образом:',
    'help.formula.killer'          => 'Очки Убийцы = Очки Убийцы + (Очки Жертвы / Очки Убийцы)',
    'help.formula.weapon_modifier' => 'Коэффициент Оружия',
    'help.formula.victim'          => 'Очки Жертвы = Очки Жертвы - (Очки Жертвы / Очки Убийцы)',
    'help.text.point_bonuses_intro' => 'Вдобавок к вышесказанному, за выполнение определенных целей в разных играх предусмотрены следующие бонусы очков:',
    'help.col.game'              => 'Игра',
    'help.col.player_action'    => 'Действие игрока',
    'help.col.plyrplyr_action'  => 'Взаимодействие Игрок-Игрок',
    'help.col.team_action'       => 'Командные действия',
    'help.col.world_action'      => 'Действие мира',
    'help.col.player_reward'    => 'Вознаграждение игроку',
    'help.col.team_reward'       => 'Вознаграждение команде',
    'help.label.note'            => 'Примечание:',
    'help.text.action_reward_note' => 'Игрок, который совершил действие, может получить как командный бонус, так и персональный.',
    'help.text.weapon_modifiers'   => 'Коэффициенты оружия используются для определения того, сколько очков вы можете получить или потерять, когда вы убиваете или убивают вас. Более высокие коэффициенты некоторых видов оружия указывают на то, что при использовании такого оружия вы заработаете больше очков опыта, и соответственно наоборот. Коэффициенты обычно разнятся от 0.00 до 2.00.',
    'help.text.set_intro'        => 'Опции профиля игрока могут быть установлены соответствующей командой из чата <strong>HLX_SET</strong> в то время, когд вы находитесь на игровом сервере, подключенном к HlstatsX. Для использования команд, нажмите кнопку чата и введите необходимую команду.',
    'help.text.set_syntax'       => 'Формат ввода команд: say <strong>/hlx_set опция значение</strong>.',
    'help.text.set_options_intro' => 'Приемлемыми командами являются:',
    'help.text.set_realname'     => 'Устанавливает ваше настоящее имя в профиле статистики.',
    'help.label.example'         => 'Пример:',
    'help.text.set_email'        => 'Устанавливает ваш e-mail в профиле статистики.',
    'help.text.set_homepage'     => 'Устанавливает вашу домашнюю страничку в профиле статистики.',
    'help.text.set_note'         => 'Данные команды не являются стандартными консольными командами Half-Life. Если вы введете их в консоли, Half-Life выдаст вам ошибку.<br /><br />Для просмотра полного списка команд, поддерживаемых в игре, напишите в игровом чате слово !help.',
    'help.text.hideranking'      => 'Напишите в чат <b>/hlx_hideranking</b> во время игры на сервере, подключенном к HLstatsX. После этого вас не будет видно на странице статистики игроков.',
    'help.text.hideranking_note_pre'  => 'Вы все же будете отслеживаться статистикой и сможете просматривать свой профиль. Используте ',
    'help.text.hideranking_note_post' => ', чтобы найти себя любимого.',
];
