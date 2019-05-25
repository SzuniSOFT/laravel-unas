<?php


namespace SzuniSoft\Unas\Model;


use SzuniSoft\Unas\Model\Support\AbstractAncestorModel;
use SzuniSoft\Unas\Model\Support\Concerns\PercentSuffixRemover;

/**
 * Class Product
 * @package SzuniSoft\Unas\Model
 *
 * @property string                                 $state
 * @property string                                 $id
 * @property string                                 $sku
 * @property boolean                                $explicit
 * @property string                                 $name
 * @property string                                 $unit
 * @property float                                  $minimum_qty
 * @property float                                  $maximum_qty
 * @property float                                  $alert_qty
 * @property string                                 $unit_step
 * @property int                                    $weight
 * @property int                                    $point
 * @property string                                 $short_description
 * @property string                                 $long_description
 * @property string                                 $vat
 * @property string                                 $url
 *
 * @property \SzuniSoft\Unas\Model\ProductAlterUnit $alter_unit
 * @property \Illuminate\Support\Collection         $prices
 * @property \Illuminate\Support\Collection         $categories
 * @property \Illuminate\Support\Collection         $images
 * @property \Illuminate\Support\Collection         $datas
 * @property \Illuminate\Support\Collection         $params
 * @property \Illuminate\Support\Collection         $variants
 * @property \Illuminate\Support\Collection         $qty_discounts
 * @property \Illuminate\Support\Collection         $additional_products
 * @property \Illuminate\Support\Collection         $similar_products
 */
class Product extends AbstractAncestorModel
{

    use PercentSuffixRemover;

    protected $casts = [
        'explicit' => 'boolean',
        'minimum_qty' => 'float',
        'maximum_qty' => 'float',
        'alert_qty' => 'float',
        'weight' => 'integer',
        'point' => 'integer',
        'vat' => 'float',
    ];

    public function __construct(array $raw, bool $exists = false)
    {
        $this->isNew = !$exists;

        parent::__construct($raw, $exists);

        if (isset($raw['Description'])) {
            if (isset($raw['Description']['Short'])) {
                $this->short_description = $raw['Description']['Short'];
            }
            if (isset($raw['Description']['Long'])) {
                $this->long_description = $raw['Description']['Long'];
            }
        }

        if (isset($raw['AlterUnit'])) {
            $this->alter_unit = new ProductAlterUnit($raw['AlterUnit']);
        }

        $this->vat = $this->removePercentSuffix($raw['Prices']['Vat'] ?? null);

        $this->prices = $this
            ->collection($raw['Prices']['Price'] ?? [])
            ->map(function ($rawPrice) {
                return new ProductPrice($rawPrice);
            });

        $this->categories = $this
            ->collection($raw['Categories']['Category'] ?? [])
            ->map(function ($rawCategory) {
                return new ProductCategory($rawCategory);
            });

        $this->images = $this
            ->collection($raw['Images']['Image'] ?? [])
            ->map(function ($rawImage) {
                return new ProductImage($rawImage);
            });

        $this->variants = $this
            ->collection($raw['Variants']['Variant'] ?? [])
            ->map(function ($rawVariant) {
                return new ProductVariant($rawVariant);
            });

        $this->datas = $this
            ->collection($raw['Datas']['Data'] ?? [])
            ->map(function ($rawData) {
                return new ProductData($rawData);
            });

        $this->params = $this
            ->collection($raw['Params']['Param'] ?? [])
            ->map(function ($rawParam) {
                return new ProductParameter($rawParam);
            });

        $this->qty_discounts = $this
            ->collection($raw['QtyDiscount']['Step'] ?? [])
            ->map(function ($rawDiscount) {
                return new ProductQtyDiscount($rawDiscount);
            });

        $this->additional_products = $this
            ->collection($raw['AdditionalProducts']['AdditionalProduct'] ?? [])
            ->map(function ($rawProduct) {
                return new AdditionalProduct($rawProduct);
            });

        $this->similar_products = $this
            ->collection($raw['SimilarProducts']['SimilarProduct'] ?? [])
            ->map(function ($rawProduct) {
                return new SimilarProduct($rawProduct);
            });
    }

    protected function validationRulesForUpdate()
    {

    }

    protected function validationRulesForCreate()
    {

    }

    protected function optimizeForUpdate()
    {

    }

    protected function optimizeForCreate()
    {

    }
}
