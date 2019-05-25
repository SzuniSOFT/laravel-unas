<?php


namespace SzuniSoft\Unas\Model;


use SzuniSoft\Unas\Model\Support\AbstractModel;

/**
 * Class Shipping
 * @package SzuniSoft\Unas\Model
 *
 * @property string $id
 * @property string $name
 * @property string $package_number
 * @property string $foreign_id
 */
class Shipping extends AbstractModel
{

    protected $attributesForCreate = [
        'name', 'package_number'
    ];

    protected $attributesForUpdate = [
        'name', 'package_number'
    ];

}
