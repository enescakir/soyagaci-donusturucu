<?php

namespace App\Enums;

class TreeStatus extends BaseEnum
{
    const START = 0;
    const SUCCESS = 1;
    const PARSING = 2;
    const CONTENT_ERROR = 4;
    const PARSE_ERROR = 5;
    const CONVERT_ERROR = 6;
    public static $statusTexts = [
        0 => 'Ağaç Oluşturuldu',
        1 => 'Başarılı',
        2 => 'Hazırlanıyor',
        3 => 'Yanlış Dosya',
        4 => 'İşleme Hatası',
        5 => 'Dönüştürme Hatası',
    ];

    // $this->statusText = isset(self::$statusTexts[$code]) ? self::$statusTexts[$code] : 'unknown status';
    public static function errors()
    {
        return [static::CONTENT_ERROR, static::PARSE_ERROR, static::CONVERT_ERROR];
    }

    public static function isError($status)
    {
        return in_array($status, static::errors());
    }
}
