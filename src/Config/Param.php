<?php

namespace App\Config;

Class Param
{
    public const HELLO_WORLD       = 'Hello world!';
    public const BASE_URL_ABSOLUTE = 'https://cstv.fr/app';
    public const HOME_URL_RELATIVE = '/home/main';
    public const PREFIX_CACHE      = './cache/';
    public const VLC_DEEPLINK      = 'vlc://';
    public const YOUTUBE_DEEPLINK  = 'vdn.youtube://';

    public const TWIG_GLOBAL_VARS = [
        'baseUrlAbsolute' => self::BASE_URL_ABSOLUTE,
        'homeUrlAbsolute' => self::HOME_URL_RELATIVE,
        'vlcDeeplink'     => self::VLC_DEEPLINK,
        'youtubeDeeplink' => self::YOUTUBE_DEEPLINK,
    ];
}
