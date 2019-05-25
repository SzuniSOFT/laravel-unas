<?php


namespace SzuniSoft\Unas\Model;


use SzuniSoft\Unas\Model\Support\AbstractModel;

/**
 * Class OrderControl
 * @package SzuniSoft\Unas\Model
 *
 * @property float $quantity
 * @property string $user
 */
class OrderItemControl extends AbstractModel
{

    protected $attributesForCreate = ['quantity', 'user'];

    protected $attributesForUpdate = ['quantity', 'user'];

    protected $casts = [
        'quantity' => 'float'
    ];

}
