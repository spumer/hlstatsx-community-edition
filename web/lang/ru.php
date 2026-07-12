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
];
