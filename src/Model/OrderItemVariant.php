<?php


namespace SzuniSoft\Unas\Model;


use SzuniSoft\Unas\Model\Support\AbstractModel;

/**
 * Class OrderItemVariant
 * @package SzuniSoft\Unas\Model
 *
 * @property string $id
 * @property string $name
 * @property string $value
 */
class OrderItemVariant extends AbstractModel
{

    protected $attributesForCreate = ['id', 'name', 'value'];

    protected $attributesForUpdate = [];

}
