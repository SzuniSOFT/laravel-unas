<?php


namespace SzuniSoft\Unas\Model;


use SzuniSoft\Unas\Model\Support\AbstractModel;

/**
 * Class OrderItemPlusStatus
 * @package SzuniSoft\Unas\Model
 *
 * @property string $id
 * @property string $name
 * @property string $value
 * @property boolean $public
 */
class OrderItemPlusStatus extends AbstractModel
{

    protected $attributesForCreate = ['id', 'name', 'value'];

    protected $attributesForUpdate = ['id', 'name', 'value'];

    protected $casts = [
        'public' => 'boolean'
    ];

}
