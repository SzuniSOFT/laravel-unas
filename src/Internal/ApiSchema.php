<?php


namespace SzuniSoft\Unas\Internal;


use Illuminate\Support\Str;
use function mb_strtolower;

/**
 * Class ApiSchema
 * @package SzuniSoft\Unas\Internal
 * @codeCoverageIgnore
 */
class ApiSchema
{

    const AUTH_DATE_TIME_FORMAT = 'd.m.Y H:i:s';
    const COMMON_DATE_TIME_FORMAT = 'd.m.Y H:i:s';
    const COMMON_DATE_FORMAT = 'Y.m.d';
    const MAX_PAGING_CHUNK_SIZE = 500;

    const PREMIUM_PACKAGE_ERROR_MESSAGE = 'Login Error: PREMIUM package needed';

    /**
     * Optimize key name for UNAS.
     * (Pascal case at this time)
     *
     * @param $key
     *
     * @return string
     */
    public static function keyify($key)
    {
        switch (mb_strtolower($key)) {
            case 'zip':
                return 'ZIP';
            case 'eu_tax_number':
                return 'EUTaxNumber';
            case 'delivery_point_id':
                return 'DeliveryPointID';
        }

        return Str::ucfirst(Str::camel($key));
    }

}
