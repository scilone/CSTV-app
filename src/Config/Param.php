<?php

namespace App\Config;

Class Param
{
    public const HELLO_WORLD       = 'Hello world!';
    public const BASE_URL_ABSOLUTE = 'https://cstv.fr/app';
    public const BASE_URL_RELATIVE = '/app';
    public const HOME_URL_RELATIVE = '/home/main';
    public const PREFIX_CACHE      = './cache/';
    public const VLC_DEEPLINK      = 'vlc://';
    public const VLC_INTENT        = '#Intent;scheme=vlc;package=org.videolan.vlc;';
    public const YOUTUBE_DEEPLINK  = 'vnd.youtube://';
    public const YOUTUBE_INTENT    = '#Intent;scheme=vnd.youtube;package=com.google.android.youtube;';



    public const TWIG_GLOBAL_VARS = [
        'baseUrlAbsolute' => self::BASE_URL_ABSOLUTE,
        'homeUrlAbsolute' => self::HOME_URL_RELATIVE,
        'vlcDeeplink'     => self::VLC_DEEPLINK,
        'vlcIntent'       => self::VLC_INTENT,
        'youtubeDeeplink' => self::YOUTUBE_DEEPLINK,
        'youtubeIntent'   => self::YOUTUBE_INTENT,
    ];
}
