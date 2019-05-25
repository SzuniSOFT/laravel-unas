<?php


namespace SzuniSoft\Unas\Model;


use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use SzuniSoft\Unas\Internal\Optimizers\OrderOptimizer;
use SzuniSoft\Unas\Model\Support\AbstractAncestorModel;

/**
 * Class Order
 * @package SzuniSoft\Unas\Model
 *
 * @property string     $key
 * @property Carbon     $date
 * @property Carbon     $date_mod
 * @property string     $currency
 * @property string     $type
 * @property string     $status
 * @property string     $status_details
 * @property boolean    $status_email
 * @property string     $coupon
 * @property string     $referer
 *
 * @property Payment    $payment
 * @property Shipping   $shipping
 * @property Invoice    $invoice
 * @property Customer   $customer
 * @property Collection $params
 * @property OrderInfo  $info
 * @property string     $customer_comment
 * @property string     $admin_comment
 * @property Collection $items
 */
class Order extends AbstractAncestorModel
{

    use OrderOptimizer;

    protected $casts = [
        'date' => 'date',
        'date_mod' => 'date',
        'status_email' => 'boolean',
    ];

    protected $attributesForCreate = [
        'date', 'currency', 'referer', 'coupon',
        'type', 'status', 'status_details', 'status_email',
    ];

    protected $attributesForUpdate = [
        'type', 'status', 'status_details', 'status_email',
    ];

    /**
     * Order constructor.
     *
     * @param array $raw
     * @param bool  $exists
     */
    public function __construct(array $raw = [], $exists = false)
    {
        $this->isNew = !$exists;

        parent::__construct($raw);

        if (isset($raw['Customer'])) {
            $this->customer = new Customer($raw['Customer']);
        }

        if (isset($raw['Payment'])) {
            $this->payment = new Payment($raw['Payment']);
        }

        if (isset($raw['Shipping'])) {
            $this->shipping = new Shipping($raw['Shipping']);
        }

        if (isset($raw['Invoice'])) {
            $this->invoice = new Invoice($raw['Invoice']);
        }

        if (isset($raw['Params']) && isset($raw['Params']['Param'])) {
            $this->params = Collection::wrap($raw['Params']['Param'])->map(function ($rawParam) {
                return new OrderParameter($rawParam);
            });
        }

        if (isset($raw['Info'])) {
            $this->info = new OrderInfo($raw['Info']);
        }

        if (isset($raw['Comments']) && isset($raw['Comments']['Comment'])) {
            list($customerComments, $adminComments) = Collection::wrap($raw['Comments']['Comment'])
                ->partition('Type', '=', 'customer');

            if ($customerComments) {
                $this->customer_comment = $customerComments->first()['Text'];
            }
            if ($adminComments) {
                $this->admin_comment = $adminComments->first()['Text'];
            }
        }

        if (isset($raw['Items']) && isset($raw['Items']['Item'])) {
            $this->items = Collection::wrap($raw['Items']['Item'])->map(function ($rawItem) {
                return new OrderItem($rawItem);
            });
        }
    }

    /**
     * @param $arr
     *
     * @return mixed
     */
    protected function normalizeEmailStatus($arr)
    {
        if (isset($arr['status_email'])) {
            $arr['status_email'] = !!$arr['status_email'] ? 'yes' : 'no';
        }
        return $arr;
    }

    /**
     * @return array
     */
    public function getAttributesForCreate()
    {
        return $this->normalizeEmailStatus(parent::getAttributesForCreate());
    }

    /**
     * @return array
     */
    public function getAttributesForUpdate()
    {
        return $this->normalizeEmailStatus(parent::getAttributesForUpdate());
    }

}
