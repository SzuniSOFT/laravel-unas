<?php


namespace SzuniSoft\Unas\Model;


use SzuniSoft\Unas\Model\Support\AbstractModel;

/**
 * Class Address
 * @package SzuniSoft\Unas\Model
 *
 * @property string $name
 * @property string $zip
 * @property string $city
 * @property string $street
 * @property string $street_name
 * @property string $street_number
 * @property string $county
 * @property string $country
 * @property string $country_code
 * @property string $eu_tax_number
 * @property string $delivery_point_id
 * @property string $delivery_point_group
 */
class Address extends AbstractModel
{

    protected $attributesForCreate = [
        'name',
        'zip',
        'city',
        'street',
        'county',
        'country_code',
        'tax_number',
        'eu_tax_number',
        'delivery_point_id',
        'delivery_point_group'
    ];

    protected $attributesForUpdate = [
        'name',
        'zip',
        'city',
        'street',
        'county',
        'country_code',
        'tax_number',
        'eu_tax_number',
        'delivery_point_id',
        'delivery_point_group'
    ];

}
