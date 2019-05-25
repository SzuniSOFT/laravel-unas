<?php


namespace SzuniSoft\Unas\Model;


use SzuniSoft\Unas\Model\Support\AbstractModel;

/**
 * Class Contact
 * @package SzuniSoft\Unas\Model
 *
 * @property string $name
 * @property string $phone
 * @property string $mobile
 * @property string $lang
 */
class Contact extends AbstractModel
{

    protected $attributesForCreate = [
        'name', 'phone', 'mobile'
    ];

    protected $attributesForUpdate = [
        'name', 'phone', 'mobile'
    ];

}
