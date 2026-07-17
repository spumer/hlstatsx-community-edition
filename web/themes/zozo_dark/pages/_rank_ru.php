<?php
/*
 * ZoZo Dark — rank name RU map + 6-tier kill-band classifier.
 *
 * Hybrid ranks (founder decision, VRTF r1 DEF-11): the full 39-rank L4D2
 * military ladder keeps its granularity as RU TEXT, while the tier GLYPH
 * collapses to one of 6 bands (task #23 icon set stays valid). Kept as a
 * theme-side PHP lookup rather than 39 catalog keys because it gives a
 * clean fallback to the original rankName for custom/unknown ranks (team
 * decision allowed this if justified); the RU strings were reviewed by
 * the facilitator (5 corrections applied) and shown to the founder.
 *
 * Included by the players / playerinfo / playerinfo_general theme copies.
 */

if (!defined('IN_HLSTATS')) {
    die('Do not access this file directly.');
}

if (!function_exists('zozo_rank_ru')) {

    /** RU translation of a stock L4D2 rankName; falls back to the original. */
    function zozo_rank_ru(string $rankName): string
    {
        static $map = [
            'Recruit'                    => 'Рекрут',
            'Private'                    => 'Рядовой',
            'Private First Class'        => 'Рядовой первого класса',
            'Lance Corporal'             => 'Ефрейтор',
            'Corporal'                   => 'Капрал',
            'Sergeant'                   => 'Сержант',
            'Staff Sergeant'             => 'Штаб-сержант',
            'Gunnery Sergeant'           => 'Комендор-сержант',
            'Master Sergeant'            => 'Мастер-сержант',
            'First Sergeant'             => 'Первый сержант',
            'Master Chief'               => 'Главный старшина',
            'Sergeant Major'             => 'Сержант-майор',
            'Ensign'                     => 'Энсин',
            'Third Lieutenant'           => 'Третий лейтенант',
            'Second Lieutenant'          => 'Второй лейтенант',
            'First Lieutenant'           => 'Первый лейтенант',
            'Captain'                    => 'Капитан',
            'Group Captain'              => 'Груп-кэптен',
            'Senior Captain'             => 'Старший капитан',
            'Lieutenant Major'           => 'Лейтенант-майор',
            'Major'                      => 'Майор',
            'Group Major'                => 'Груп-майор',
            'Lieutenant Commander'       => 'Лейтенант-коммандер',
            'Commander'                  => 'Коммандер',
            'Group Commander'            => 'Груп-коммандер',
            'Lieutenant Colonel'         => 'Подполковник',
            'Colonel'                    => 'Полковник',
            'Brigadier'                  => 'Бригадир',
            'Brigadier General'          => 'Бригадный генерал',
            'Major General'              => 'Генерал-майор',
            'Lieutenant General'         => 'Генерал-лейтенант',
            'General'                    => 'Генерал',
            'Commander General'          => 'Генерал-коммандер',
            'Field Vice Marshal'         => 'Вице-фельдмаршал',
            'Field Marshal'              => 'Фельдмаршал',
            'Vice Commander of the Army' => 'Вице-командующий армией',
            'Commander of the Army'      => 'Командующий армией',
            'High Commander'             => 'Верховный командующий',
            'Supreme Commander'          => 'Главнокомандующий',
        ];

        return $map[$rankName] ?? $rankName;
    }

    /**
     * 6-tier band (1..6) for the rank glyph colour, derived from the stock
     * L4D2 minKills thresholds so the glyph escalates with the ladder:
     *   1 (common)   0–399     Recruit … Corporal   (enlisted start)
     *   2 (smoker)   400–999   Sergeant … Master Chief
     *   3 (charger)  1000–1999 Sergeant Major … First Lieutenant
     *   4 (hunter)   2000–4999 Captain … Commander
     *   5 (witch)    5000–14999 Group Commander … General
     *   6 (tank)     15000+    Commander General … Supreme Commander
     */
    function zozo_rank_tier(int $kills): int
    {
        if ($kills >= 15000) return 6;
        if ($kills >= 5000)  return 5;
        if ($kills >= 2000)  return 4;
        if ($kills >= 1000)  return 3;
        if ($kills >= 400)   return 2;
        return 1;
    }
}
