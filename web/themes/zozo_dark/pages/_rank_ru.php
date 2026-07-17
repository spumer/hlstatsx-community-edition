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
        // Zombie survival ladder (founder-approved 2026-07-17): the 39 stock
        // military rankNames map to L4D2 zombie-themed RU titles. EN keys stay
        // the stock rankName; zozo_rank_tier() (glyph colour) is unchanged.
        static $map = [
            'Recruit'                    => 'Новобранец',
            'Private'                    => 'Выживший',
            'Private First Class'        => 'Беглец',
            'Lance Corporal'             => 'Скиталец',
            'Corporal'                   => 'Мародёр',
            'Sergeant'                   => 'Стрелок',
            'Staff Sergeant'             => 'Меткач',
            'Gunnery Sergeant'           => 'Снайпер',
            'Master Sergeant'            => 'Следопыт',
            'First Sergeant'             => 'Охотник',
            'Master Chief'               => 'Чистильщик',
            'Sergeant Major'             => 'Боец',
            'Ensign'                     => 'Штурмовик',
            'Third Lieutenant'           => 'Громила',
            'Second Lieutenant'          => 'Берсерк',
            'First Lieutenant'           => 'Крушитель',
            'Captain'                    => 'Убийца',
            'Group Captain'              => 'Головорез',
            'Senior Captain'             => 'Мясник',
            'Lieutenant Major'           => 'Палач',
            'Major'                      => 'Каратель',
            'Group Major'                => 'Жнец',
            'Lieutenant Commander'       => 'Истребитель',
            'Commander'                  => 'Потрошитель',
            'Group Commander'            => 'Ветеран',
            'Lieutenant Colonel'         => 'Ликвидатор',
            'Colonel'                    => 'Опустошитель',
            'Brigadier'                  => 'Гроза орды',
            'Brigadier General'          => 'Кошмар',
            'Major General'              => 'Апокалиптик',
            'Lieutenant General'         => 'Легенда',
            'General'                    => 'Погибель',
            'Commander General'          => 'Разрушитель',
            'Field Vice Marshal'         => 'Титан',
            'Field Marshal'              => 'Бессмертный',
            'Vice Commander of the Army' => 'Владыка',
            'Commander of the Army'      => 'Апокалипсис',
            'High Commander'             => 'Повелитель мёртвых',
            'Supreme Commander'          => 'Зомби-бог',
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
