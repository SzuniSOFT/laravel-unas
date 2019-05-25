<?php


namespace SzuniSoft\Unas\Model;


use SzuniSoft\Unas\Model\Support\AbstractModel;
use SzuniSoft\Unas\Model\Support\Concerns\PercentSuffixRemover;

/**
 * Class ProductQtyDiscount
 * @package SzuniSoft\Unas\Model
 *
 * @property double $limit_lower
 * @property double $limit_upper
 * @property double $discount
 */
class ProductQtyDiscount extends AbstractModel
{

    use PercentSuffixRemover;


    protected $casts = [
        'discount' => 'double',
        'limit_upper' => 'double',
        'limit_lower' => 'double',
    ];

    protected $ignored = [
        'discount',
    ];

    public function __construct(array $raw, $exists = false)
    {
        parent::__construct([
            'discount' => isset($raw['Discount'])
                ? $this->removePercentSuffix($raw['Discount'])
                : null,
            'limit_lower' => (isset($raw['Limit']) && isset($raw['Limit']['Lower']))
                ? $raw['Limit']['Lower']
                : null,
            'limit_upper' => (isset($raw['Limit']) && isset($raw['Limit']['Upper']))
                ? $raw['Limit']['Upper']
                : null,
        ], $exists);

        if (isset($raw['Discount'])) {
            $this->discount = $this->removePercentSuffix($raw['Discount']);
        }
    }

}
