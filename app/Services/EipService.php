<?php

namespace App\Services;

use App\Services\EipVendor\Nueip;

class EipService
{

    const EIP_LISTS = [
        Nueip::VENDOR_NAME => Nueip::class
    ];

    public static function getFromName($name)
    {
        return self::EIP_LISTS[$name] ?? null;
    } 
}
