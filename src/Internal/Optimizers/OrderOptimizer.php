<?php


namespace SzuniSoft\Unas\Internal\Optimizers;


use Illuminate\Validation\Rule;
use SzuniSoft\Unas\Internal\ApiSchema;
use SzuniSoft\Unas\Model\CustomerParameter;
use SzuniSoft\Unas\Model\Invoice;
use SzuniSoft\Unas\Model\Order;
use SzuniSoft\Unas\Model\OrderItem;
use SzuniSoft\Unas\Model\OrderItemPlusStatus;
use SzuniSoft\Unas\Model\OrderItemVariant;
use SzuniSoft\Unas\Model\OrderParameter;
use SzuniSoft\Unas\Model\Transaction;
use function array_values;


/**
 * Trait OrderOptimizer
 * @package SzuniSoft\Unas\Internal\Optimizers
 * @mixin Order
 */
trait OrderOptimizer
{

    protected function validationRulesForUpdate()
    {
        return $this->validationRulesForCreate();
    }

    protected function optimizeForUpdate()
    {
        return $this->optimizeForCreate();
    }


    /**
     * @return array
     */
    protected function validationRulesForCreate()
    {
        return [
            'action' => ['required', Rule::in('add', 'modify')],
            'key' => ['required_without:date'],
            'date' => ['required_without:key'],
            'currency' => ['required'],
            'type' => ['sometimes', 'string'],
            'status' => ['sometimes', 'string'],
            'status_details' => ['sometimes', 'string'],
            'status_email' => ['sometimes', Rule::in('yes', 'no')],
            'referer' => ['sometimes'],
            'coupon' => ['sometimes'],

            'customer.email' => ['sometimes', 'email'],
            'customer.username' => ['sometimes', 'string'],
            'customer.comment' => ['sometimes', 'string'],

            'customer.addresses' => ['sometimes'],
            'customer.addresses.invoice' => ['sometimes'],
            'customer.addresses.invoice.name' => ['required'],
            'customer.addresses.invoice.zip' => ['required'],
            'customer.addresses.invoice.city' => ['required'],
            'customer.addresses.invoice.street_name' => ['required'],
            'customer.addresses.invoice.street_type' => ['sometimes', 'string'],
            'customer.addresses.invoice.street_number' => ['sometimes'],
            'customer.addresses.invoice.county' => ['sometimes'],
            'customer.addresses.invoice.country' => ['sometimes'],
            'customer.addresses.invoice.country_code' => ['sometimes', 'string', 'max:2'],
            'customer.addresses.invoice.tax_number' => ['sometimes'],
            'customer.addresses.invoice.eu_tax_number' => ['sometimes'],

            'customer.addresses.shipping' => ['sometimes'],
            'customer.addresses.shipping.name' => ['required'],
            'customer.addresses.shipping.zip' => ['required'],
            'customer.addresses.shipping.city' => ['required'],
            'customer.addresses.shipping.street_name' => ['required'],
            'customer.addresses.shipping.street_type' => ['sometimes', 'string'],
            'customer.addresses.shipping.street_number' => ['sometimes'],
            'customer.addresses.shipping.county' => ['sometimes'],
            'customer.addresses.shipping.country' => ['sometimes'],
            'customer.addresses.shipping.country_code' => ['sometimes', 'string', 'max:2'],
            'customer.addresses.shipping.delivery_point_id' => ['sometimes'],
            'customer.addresses.shipping.delivery_point_group' => ['sometimes'],

            'customer.params' => ['sometimes', 'array' . 'min:1'],
            'customer.params.*.id' => ['required'],
            'customer.params.*.name' => ['required'],
            'customer.params.*.value' => ['required'],

            'payment' => ['sometimes'],
            'payment.name' => ['required'],
            'payment.transactions' => ['sometimes'],
            'payment.transactions.transaction' => ['sometimes', 'array', 'min:1'],
            'payment.transactions.transaction.*.id' => ['required'],
            'payment.transactions.transaction.*.amount' => ['required', 'numeric'],

            'shipping' => ['sometimes'],
            'shipping.name' => ['required'],
            'shipping.package_number' => ['sometimes'],

            'invoice' => ['sometimes'],
            'invoice.status' => ['required', Rule::in(array_values(Invoice::STATUS))],
            'invoice.number' => ['sometimes'],

            'params' => ['sometimes'],
            'params.param' => ['sometimes', 'array', 'min:1'],
            'params.param.*.id' => ['required'],
            'params.param.*.name' => ['required'],
            'params.param.*.value' => ['required'],

            'items' => ['sometimes'],
            'items.item' => ['sometimes', 'array', 'min:1'],
            'items.item.*.id' => ['required_without:sku'],
            'items.item.*.sku' => ['required_without:id'],
            'items.item.*.name' => ['sometimes'],
            'items.item.*.unit' => ['required'],
            'items.item.*.quantity' => ['required'],
            'items.item.*.price_net' => ['required'],
            'items.item.*.price_gross' => ['required'],
            'items.item.*.status' => ['sometimes'],
            'items.item.*.plus_statuses' => ['sometimes'],
            'items.item.*.plus_statuses.status' => ['array', 'min:1'],
            'items.item.*.control' => ['sometimes'],
            'items.item.*.control.quantity' => ['numeric'],
            'items.item.*.control.user' => ['string'],
            'items.item.*.product_params' => ['sometimes'],
            'items.item.*.product_params.product_param' => ['array', 'min:1'],
            'items.item.*.product_params.product_param.*.id' => ['required'],
            'items.item.*.product_params.product_param.*.name' => ['required'],
            'items.item.*.product_params.product_param.*.value' => ['required'],
        ];
    }

    /**
     * @return array
     */
    public function optimizeForCreate()
    {
        // Prepare meta data.
        $arr = ['action' => $this->isNew ? 'add' : 'modify'];
        if (!$this->isNew) {
            $arr['key'] = $this->key;
        }
        else {
            $arr['date'] = $this->date->format(ApiSchema::COMMON_DATE_TIME_FORMAT);
        }

        // Optimize customer of order.
        if ($this->customer) {
            $arr['customer'] = $this->customer->getAttributesForCreate();
            if ($this->customer->contact) {
                $arr['customer']['contact'] = $this->customer->contact->getAttributesForCreate();
            }
            // Fill up with addresses.
            if ($this->customer->invoice_address || $this->customer->shipping_address) {
                $arr['customer']['addresses'] = [];
                if ($this->customer->invoice_address) {
                    $arr['customer']['addresses']['invoice'] = $this->customer->invoice_address->getAttributesForCreate();
                }
                if ($this->customer->shipping_address) {
                    $arr['customer']['addresses']['shipping'] = $this->customer->shipping_address->getAttributesForCreate();
                }
            }
            if ($this->customer->params && $this->customer->params->isNotEmpty()) {
                $arr['customer']['params'] = ['param' => $this->customer->params->map(function (CustomerParameter $parameter) {
                    return $parameter->getAttributesForCreate();
                })->toArray()];
            }
        }

        // Optimize payment of order.
        if ($this->payment) {
            $arr['payment'] = $this->payment->getAttributesForCreate();
            // Fill up with payment transactions.
            if ($this->payment->transactions && $this->payment->transactions->isNotEmpty()) {
                $arr['payment']['transactions'] = ['transaction' => $this->payment->transactions
                    // Take only the manual transactions for update.
                    // Also filter on new ones.
                    ->filter(function (Transaction $transaction) {
                        return $transaction->is_manual && $transaction->isNew();
                    })
                    ->map(function (Transaction $transaction) {
                        return $transaction->getAttributesForCreate();
                    })->toArray()
                ];
            }
        }

        // Shipping details.
        if ($this->shipping) {
            $arr['shipping'] = $this->shipping->getAttributesForCreate();
        }

        // Invoice details.
        if ($this->invoice) {
            $arr['invoice'] = $this->invoice->getAttributesForCreate();
        }

        // Setup parameters on order.
        if ($this->params && $this->params->isNotEmpty()) {
            $arr['params'] = ['param' => $this->params->map(function (OrderParameter $parameter) {
                return $parameter->getAttributesForCreate();
            })->toArray()];
        }

        // Comments attached to order.
        if ($this->customer_comment || $this->admin_comment) {
            $arr['comments'] = ['comment' => []];
            if ($this->customer_comment) {
                $arr['comments']['comment'][] = ['type' => 'customer', 'text' => $this->customer_comment];
            }
            if ($this->admin_comment) {
                $arr['comments']['comment'][] = ['type' => 'admin', 'text' => $this->admin_comment];
            }
        }

        // Items.
        if ($this->items && $this->items->isNotEmpty()) {
            $arr['items'] = ['item' => $this->items->map(function (OrderItem $item) {
                $itemArr = $item->getAttributesForCreate();

                // Add extra statuses to item.
                if ($item->plus_statuses && $item->plus_statuses->isNotEmpty()) {
                    $itemArr['plus_statuses'] = ['status' => $item->plus_statuses->map(function (OrderItemPlusStatus $plusStatus) {
                        return $plusStatus->getAttributesForCreate();
                    })->toArray()];
                }

                // Specify stock control information if available.
                if ($item->control) {
                    $itemArr['control'] = $item->control->getAttributesForCreate();
                }

                // Apply item variants.
                if ($item->variants && $item->variants->isNotEmpty()) {
                    $itemArr['variants'] = ['variant' => $item->variants->take(3)->map(function (OrderItemVariant $variant) {
                        return $variant->getAttributesForCreate();
                    })->toArray()];
                }

                // Add item parameters.
                // Todo: make sure it cannot be sent.
                /*if ($item->product_params && $item->product_params->isNotEmpty()) {
                    $itemArr['product_params'] = ['product_param' => $item->product_params->map(function (OrderItemProductParameter $parameter) {
                        return $parameter->getAttributesForCreate();
                    })->toArray()];
                }*/

                return $itemArr;
            })->toArray()];
        }

        return $arr;
    }

}
