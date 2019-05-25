<?php


namespace SzuniSoft\Unas\Model;


use Carbon\Carbon;
use SzuniSoft\Unas\Model\Support\AbstractModel;

/**
 * Class Transaction
 * @package SzuniSoft\Unas\Model
 *
 * @property string $id
 * @property string $auth_code
 * @property string $status
 * @property Carbon $date
 * @property float $amount
 *
 * @property-read boolean $is_manual
 */
class Transaction extends AbstractModel
{

    protected $attributesForCreate = ['id', 'amount'];

    protected $attributesForUpdate = ['id', 'amount'];

    protected $casts = [
        'date' => 'date'
    ];

    /**
     * @return bool
     */
    public function getIsManualAttribute()
    {
        return $this->id === 'manual';
    }

    /**
     * @return $this
     */
    public static function asManual()
    {
        $t = new self(['id' => 'manual']);
        return $t;
    }

}
