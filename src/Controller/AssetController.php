<?php

namespace App\Controller;

use App\Application\Iptv;
use App\Application\Twig;

class AssetController
{
    public function img($url)
    {
        $strFile    = base64_decode($url);
        $strFileExt = end(explode('.', $strFile));

        if ($strFileExt == 'jpg' or $strFileExt == 'jpeg') {
            header('Content-Type: image/jpeg');
        } elseif ($strFileExt == 'png') {
            header('Content-Type: image/png');
        } elseif ($strFileExt == 'gif') {
            header('Content-Type: image/gif');
        } else {
            die('not supported');
        }

        if ($strFile !== '') {
            $cacheEnds = 60 * 60 * 24 * 365;
            header("Pragma: public");
            header("Cache-Control: maxage=" . $cacheEnds);
            header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $cacheEnds) . ' GMT');

            echo file_get_contents($strFile);
        }
    }
}
