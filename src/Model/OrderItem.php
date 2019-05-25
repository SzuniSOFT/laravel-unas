<?php


namespace SzuniSoft\Unas\Model;


use Illuminate\Support\Collection;
use SzuniSoft\Unas\Model\Support\AbstractModel;

/**
 * Class OrderItem
 * @package SzuniSoft\Unas\Model
 *
 * @property string $id
 * @property string $sku
 * @property string $name
 * @property string $unit
 * @property float $quantity
 * @property float $price_net
 * @property float $price_gross
 * @property string $vat
 * @property string $status
 * @property float $percent
 *
 * @property-read boolean $is_shipping_cost
 * @property-read boolean $is_discount_percent
 * @property-read boolean $is_discount_amount
 * @property-read boolean $is_handel_cost
 *
 * @property-read Collection $plus_statuses
 * @property-read OrderItemControl $control
 * @property-read Collection $variants
 * @property-read Collection $product_params
 */
class OrderItem extends AbstractModel
{

    protected $attributesForCreate = ['id', 'sku', 'name', 'unit', 'quantity', 'price_net', 'price_gross', 'status'];

    protected $attributesForUpdate = ['id', 'sku', 'name', 'unit', 'quantity', 'price_net', 'price_gross', 'status'];

    protected $casts = [
        'quantity' => 'float',
        'price_net' => 'float',
        'price_gross' => 'float',
        'percent' => 'float'
    ];

    public function __construct(array $raw = [])
    {
        parent::__construct($raw);

        if (isset($raw['PlusStatuses']) && isset($raw['PlusStatuses']['Status'])) {
            $this->plus_statuses = Collection::wrap($raw['PlusStatuses']['Status'])->map(function ($rawStatus) {
                return new OrderItemPlusStatus($rawStatus);
            });
        }

        if (isset($raw['Control'])) {
            $this->control = new OrderItemControl($raw['Control']);
        }

        if (isset($raw['Variants']) && isset($raw['Variants']['Variant'])) {
            $this->variants = Collection::wrap($raw['Variants']['Variant'])->map(function ($rawVariant) {
                return new OrderItemVariant($rawVariant);
            });
        }

        if (isset($raw['ProductParams']) && isset($raw['ProductParams']['ProductParam'])) {
            $this->product_params = Collection::wrap($raw['ProductParams']['ProductParam'])->map(function ($rawVariant) {
                return new OrderItemProductParameter($rawVariant);
            });
        }
    }

    /**
     * @return string
     */
    public function getIsDiscountPercentAttribute()
    {
        return $this->id = 'discount-percent';
    }

    /**
     * @param $percent
     * @param null $name
     *
     * @return OrderItem
     */
    public static function asDiscountPercent($percent, $name = null)
    {
        $item = new self();
        $item->id = 'discount-percent';
        $item->name = $name ?: $item->name;
        $item->percent = $percent;
        return $item;
    }

    /**
     * @return bool
     */
    public function getIsShippingCostAttribute()
    {
        return $this->id === 'shipping-cost';
    }

    /**
     * @param $cost
     *
     * @return $this
     */
    public static function asShippingCost($cost)
    {
        $item = new self();
        $item->id = 'shipping-cost';
        $item->price_gross = $cost;
        return $item;
    }

    /**
     * @return bool
     */
    public function getIsDiscountAmountAttribute()
    {
        return $this->id === 'discount-amount';
    }

    /**
     * @param $amount
     *
     * @return $this
     */
    public static function asDiscountAmount($amount)
    {
        $item = new self();
        $item->id = 'discount-amount';
        $item->price_gross = $amount;
        return $item;
    }


    /**
     * @return bool
     */
    public function getIsHandelCostAttribute()
    {
        return $this->id === 'handel-cost';
    }

    /**
     * @param $cost
     *
     * @return $this
     */
    public static function asHandelCost($cost)
    {
        $item = new self();
        $item->id = 'handel-cost';
        $item->price_gross = $cost;
        return $item;
    }

    /**
     * @return array|null
     */
    protected function typeDependentAttributes()
    {
        $arr = [];

        switch (true) {
            case $this->is_discount_percent:
                $arr['percent'] = $this->percent;
                $arr['id'] = $this->id;
                $arr['sku'] = $this->id;
                break;
            case $this->is_discount_amount:
            case $this->is_handel_cost:
            case $this->is_shipping_cost:
                $arr['price_gross'] = $this->price_gross;
                $arr['id'] = $this->id;
                $arr['sku'] = $this->id;
                break;
        }
        return empty($arr) ? null : $arr;
    }

    /**
     * @return array
     */
    public function getAttributesForCreate()
    {
        return $this->typeDependentAttributes() ?: parent::getAttributesForCreate();
    }

    /**
     * @return array
     */
    public function getAttributesForUpdate()
    {
        return $this->typeDependentAttributes() ?: parent::getAttributesForUpdate();
    }

}
