<?php


namespace SzuniSoft\Unas\Model;

use Illuminate\Support\Collection;
use SzuniSoft\Unas\Model\Support\AbstractModel;

/**
 * Class ProductVariant
 * @package SzuniSoft\Unas\Model
 *
 * @property string                         $name
 *
 * @property \Illuminate\Support\Collection $values
 */
class ProductVariant extends AbstractModel
{

    public function __construct(array $raw, bool $exists = false)
    {
        parent::__construct($raw, $exists);

        if (isset($raw['Values']) && isset($raw['Values']['Value'])) {
            $this->values = Collection::wrap($raw['Values']['Value'])->map(function ($rawValue) {
                return new ProductVariantValue($rawValue);
            });
        }
    }

}
