<?php

namespace App\Enums;

class Relation extends BaseEnum
{
    const ROOT = 'Kendisi';
    const FATHER = 'Babası';
    const MOTHER = 'Annesi';
    const SON = 'Oğlu';
    const DAUGHTER = 'Kızı';

    public static function females()
    {
        return [static::MOTHER, static::DAUGHTER];
    }

    public static function males()
    {
        return [static::FATHER, static::DAUGHTER];
    }

    public static function ancestors()
    {
        return [static::FATHER, static::MOTHER];
    }

    public static function descendants()
    {
        return [static::SON, static::DAUGHTER];
    }

    public static function isMale($relation)
    {
        return preg_match('/(' . implode('|', static::males()) . ')/', $relation);
    }

    public static function isFemale($relation)
    {
        return preg_match('/(' . implode('|', static::females()) . ')/', $relation);
    }

    public static function calculateLevel($relation)
    {
        if ($relation == 'Kendisi') {
            return 0;
        }
        $upper = preg_match_all('/(' . implode('|', static::ancestors()) . ')/', $relation);
        $lower = preg_match_all('/(' . implode('|', static::descendants()) . ')/', $relation);
        return $upper - $lower;
    }
}
