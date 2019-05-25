<?php


namespace SzuniSoft\Unas\Model;


use SzuniSoft\Unas\Model\Support\AbstractModel;

/**
 * Class ProductAlterUnit
 * @package SzuniSoft\Unas\Model
 *
 * @property double $qty
 * @property string $unit
 */
class ProductAlterUnit extends AbstractModel
{

    protected $casts = [
        'qty' => 'double',
    ];

}
