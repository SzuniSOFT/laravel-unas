<?php


namespace SzuniSoft\Unas\Model;


use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use SzuniSoft\Unas\Model\Support\AbstractModel;
use SzuniSoft\Unas\Model\Support\Concerns\PercentSuffixRemover;
use function collect;

/**
 * Class ProductPrice
 * @package SzuniSoft\Unas\Model
 *
 * @property string                     $type
 * @property float                      $net
 * @property float                      $gross
 * @property string                     $area
 * @property string                     $area_name
 * @property string                     $group
 * @property string                     $group_name
 * @property float                      $sale_net
 * @property float                      $sale_gross
 * @property float                      $percent
 * @property \Illuminate\Support\Carbon $start
 * @property \Illuminate\Support\Carbon $sale_start
 * @property \Illuminate\Support\Carbon $end
 * @property \Illuminate\Support\Carbon $sale_end
 */
class ProductPrice extends AbstractModel
{

    use PercentSuffixRemover;

    protected $ignored = [
        'start',
        'end',
        'sale_start',
        'sale_end',
    ];

    const TYPE = [
        'NORMAL' => 'normal',
        'SALE' => 'sale',
        'SPECIAL' => 'special',
    ];

    protected $casts = [
        'net' => 'float',
        'gross' => 'float',
        'sales_net' => 'float',
        'sales_gross' => 'float',
    ];

    public function __construct(array $raw, bool $exists = false)
    {
        parent::__construct($raw, $exists);

        collect(['Start', 'End', 'SaleStart', 'SaleEnd'])
            ->each(function ($key) use (&$raw) {
                if (isset($raw[$key]) && !!$raw[$key]) {
                    $targetKey = Str::snake($key);
                    $this->{$targetKey} = Carbon::createFromFormat('Y.m.d', $raw[$key]);
                }
            });
    }

    /**
     * @param \Illuminate\Support\Carbon $value
     */
    protected function setStartAttribute(Carbon $value)
    {
        $this->attributes['start'] = $value->startOfDay();
    }

    /**
     * @param \Illuminate\Support\Carbon $value
     */
    protected function setEndAttribute(Carbon $value)
    {
        $this->attributes['end'] = $value->startOfDay();
    }

    /**
     * @param $value
     */
    protected function setPercentAttribute($value)
    {
        $this->attributes['percent'] = $this->removePercentSuffix($value);
    }

}
