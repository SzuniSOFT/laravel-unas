<?php


namespace SzuniSoft\Unas\Model\Support;


/**
 * Class AbstractParam
 * @package SzuniSoft\Unas\Model
 *
 * @property string $id
 * @property string $name
 * @property string $value
 */
abstract class AbstractParameter extends AbstractModel
{

    protected $attributesForCreate = ['id', 'name', 'value'];

    protected $attributesForUpdate = ['id', 'name', 'value'];

}
