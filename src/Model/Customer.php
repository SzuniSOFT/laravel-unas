<?php


namespace SzuniSoft\Unas\Model;


use Illuminate\Support\Collection;
use SzuniSoft\Unas\Model\Support\AbstractModel;

/**
 * Class Customer
 * @package SzuniSoft\Unas\Model
 *
 * @property string $id
 * @property string $email
 * @property string $username
 * @property string $comment
 *
 * @property Contact $contact
 * @property Address $invoice_address
 * @property Address $shipping_address
 * @property CustomerGroup $group
 * @property Collection $params
 */
class Customer extends AbstractModel
{

    protected $attributesForCreate = [
        'email', 'username', 'comment'
    ];

    protected $attributesForUpdate = [
        'email', 'username', 'comment'
    ];

    public function __construct(array $raw)
    {
        parent::__construct($raw);

        if (isset($raw['Contact'])) {
            $this->contact = new Contact($raw['Contact']);
        }

        if (isset($raw['Addresses'])) {

            if (isset($raw['Addresses']['Invoice'])) {
                $this->invoice_address = new Address($raw['Addresses']['Invoice']);
            }

            if (isset($raw['Addresses']['Shipping'])) {
                $this->shipping_address = new Address($raw['Addresses']['Shipping']);
            }
        }

        if (isset($raw['Group'])) {
            $this->group = new CustomerGroup($raw['Group']);
        }

        if (isset($raw['Params']) && isset($raw['Params']['Param'])) {
            $this->params = Collection::wrap($raw['Params']['Param'])->map(function ($rawParam) {
                return new CustomerParameter($rawParam);
            });
        }
    }


}
