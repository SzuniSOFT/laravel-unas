<?php


namespace SzuniSoft\Unas\Model;


use Illuminate\Support\Collection;
use SzuniSoft\Unas\Model\Support\AbstractModel;

/**
 * Class Payment
 * @package SzuniSoft\Unas\Model
 *
 * @property int $id
 * @property string $name
 * @property string $type
 * @property string $status
 * @property float $paid
 * @property float $pending
 * @property string $foreign_id
 *
 * @property Collection $transactions
 *
 * @property-read bool $is_paid
 * @property-read bool $is_paid_partially
 * @property-read bool $is_unpaid
 * @property-read bool $is_overpaid
 */
class Payment extends AbstractModel
{

    protected $attributesForCreate = ['name'];

    protected $attributesForUpdate = ['name'];

    /**
     * Payment constructor.
     *
     * @param array $raw
     * @param bool $exists
     */
    public function __construct(array $raw, $exists = false)
    {
        parent::__construct($raw);

        // Fill up with transactions
        if (isset($raw['Transactions']) && isset($raw['Transactions']['Transaction'])) {

            $this->transactions = Collection::wrap(isset($raw['Transactions']['Transaction']))
                ->map(function ($rawTransaction) use (&$exists) {
                    return new Transaction($rawTransaction, $exists);
                });
        }
    }


    /**
     * @return bool
     */
    public function getIsPaidAttribute()
    {
        return $this->status === 'paid';
    }

    /**
     * @return bool
     */
    public function getIsPaidPartiallyAttribute()
    {
        return $this->status === 'partly paid';
    }

    /**
     * @return bool
     */
    public function getIsUnpaidAttribute()
    {
        return $this->status === 'unpaid';
    }

    /**
     * @return bool
     */
    public function getIsOverpaidAttribute()
    {
        return $this->status === 'overpaid';
    }

}
