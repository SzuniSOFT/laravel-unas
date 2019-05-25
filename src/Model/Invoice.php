<?php


namespace SzuniSoft\Unas\Model;


use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use SzuniSoft\Unas\Model\Support\AbstractModel;
use function array_values;

/**
 * Class Invoice
 * @package SzuniSoft\Unas\Model
 *
 * @property int $status
 * @property string $status_text
 * @property string $number
 *
 * @property-read bool $cannot_invoicing
 * @property-read bool $can_invoicing
 * @property-read bool $is_invoiced
 */
class Invoice extends AbstractModel
{

    protected $attributesForCreate = [
        'status', 'number'
    ];

    protected $attributesForUpdate = [
        'status', 'number'
    ];

    const STATUS = [
        'CANNOT_INVOICING' => 1,
        'CAN_INVOICING' => 2,
        'IS_INVOICED' => 3
    ];

    /**
     * @return $this
     */
    public function asCannotInvoicing()
    {
        $this->status = self::STATUS['CANNOT_INVOICING'];
        return $this;
    }

    /**
     * @return $this
     */
    public function asCanInvoicing()
    {
        $this->status = self::STATUS['CAN_INVOICING'];
        return $this;
    }

    /**
     * @return $this
     */
    public function asIsInvoiced()
    {
        $this->status = self::STATUS['IS_INVOICED'];
        return $this;
    }

    /**
     * @param $value
     *
     * @throws ValidationException
     */
    public function setStatusAttribute($value)
    {
        $v = Validator::make(['status' => $value], ['status' => self::commonValidationRules()['status']]);
        if ($v->fails()) {
            throw new ValidationException($v);
        }
        $this->status = $value;
    }

    /**
     * @return array
     */
    public static function commonValidationRules()
    {
        return ['status' => Rule::in(array_values(self::STATUS))];
    }

    /**
     * @return bool
     */
    public function getCannotInvoicingAttribute()
    {
        return $this->status == 0;
    }

    /**
     * @return bool
     */
    public function getCanInvoicingAttribute()
    {
        return $this->status == 1;
    }

    /**
     * @return bool
     */
    public function getIsInvoicedAttribute()
    {
        return $this->status == 2;
    }

}
