<?php


namespace SzuniSoft\Unas\Model;

use SzuniSoft\Unas\Model\Support\AbstractModel;

/**
 * Class ProductVariantValue
 * @package SzuniSoft\Unas\Model
 *
 * @property string $name
 * @property float  $extra_price
 */
class ProductVariantValue extends AbstractModel
{

    protected $casts = [
        'extra_price' => 'float',
    ];

}
