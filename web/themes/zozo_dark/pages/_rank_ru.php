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

if (!function_exists('zozo_rank_ru_by_kills')) {

    /**
     * Zombie-survival RU rank title for a given kill count (task #36 re-key,
     * founder-approved 2026-07-19, kill-band variant). Keyed on kills, NOT on
     * the stock rankName: ZoZo prod replaced hlstats_Ranks with 40 custom RU
     * military ranks, so the old EN-name map missed on real data. Bands mirror
     * the prod minKills thresholds; apex "Зомби-бог" sits on pos.40 (Терминатор,
     * 35000+), the new pre-apex "Аннигилятор" on pos.39 (30000+). This is
     * drift-proof: renames/typos of the DB rankName can't break it.
     * zozo_rank_tier() (glyph colour) is unchanged.
     */
    function zozo_rank_ru_by_kills(int $kills): string
    {
        static $bands = [ // [minKills, zombie title] ascending
            [0,'Новобранец'],[50,'Выживший'],[100,'Беглец'],[200,'Скиталец'],[300,'Мародёр'],
            [400,'Стрелок'],[500,'Меткач'],[600,'Снайпер'],[700,'Следопыт'],[800,'Охотник'],[900,'Чистильщик'],
            [1000,'Боец'],[1200,'Штурмовик'],[1400,'Громила'],[1600,'Берсерк'],[1800,'Крушитель'],
            [2000,'Убийца'],[2250,'Головорез'],[2500,'Мясник'],[2750,'Палач'],[3000,'Каратель'],[3500,'Жнец'],
            [4000,'Истребитель'],[4500,'Потрошитель'],[5000,'Ветеран'],[5750,'Ликвидатор'],[6500,'Опустошитель'],
            [7250,'Гроза орды'],[8000,'Кошмар'],[9000,'Апокалиптик'],[10000,'Легенда'],[12500,'Погибель'],
            [15000,'Разрушитель'],[17500,'Титан'],[20000,'Бессмертный'],[22500,'Владыка'],[25000,'Апокалипсис'],
            [27500,'Повелитель мёртвых'],[30000,'Аннигилятор'],[35000,'Зомби-бог'],
        ];

        $name = $bands[0][1];
        foreach ($bands as [$min, $zombie]) {
            if ($kills >= $min) {
                $name = $zombie;
            } else {
                break;
            }
        }
        return $name;
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
