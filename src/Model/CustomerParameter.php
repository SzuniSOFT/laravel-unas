<?php


namespace SzuniSoft\Unas\Model;


use SzuniSoft\Unas\Model\Support\AbstractParameter;

class CustomerParameter extends AbstractParameter
{

    protected $attributesForCreate = ['id', 'name', 'value'];

    protected $attributesForUpdate = ['id', 'name', 'value'];

}
