<?php


namespace SzuniSoft\Unas\Model;


use SzuniSoft\Unas\Model\Support\AbstractModel;

/**
 * Class ProductCategory
 * @package SzuniSoft\Unas\Model
 *
 * @property string $type
 * @property string $id
 * @property string $name
 */
class ProductCategory extends AbstractModel
{

    const TYPE = [
        'BASE' => 'base',
        'ALT' => 'alt',
    ];

}
