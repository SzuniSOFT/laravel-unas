<?php


namespace SzuniSoft\Unas\Internal\Builders;


use Illuminate\Support\Carbon;
use SzuniSoft\Unas\Exceptions\InvalidBuilderMethodException;
use SzuniSoft\Unas\Internal\ApiSchema;

/**
 * Class GetOrderBuilder
 * @package SzuniSoft\Unas\Internal\Builders
 *
 * @method $this status($status)
 * @method $this statusKey($status)
 * @method $this invoiceStatus($status)
 * @method $this invoiceAutoSet($status)
 * @method $this timeStart(Carbon $dateTime)
 * @method $this timeEnd(Carbon $dateTime)
 * @method $this dateStart(Carbon $date)
 * @method $this $dateEnd(Carbon $date)
 * @method $this timeModStart(Carbon $dateTime)
 * @method $this timeModEnd(Carbon $dateTime)
 * @method $this limitStart(int $from)
 * @method $this limitNum(int $limit)
 * @method $this key($id)
 */
class GetOrderBuilder extends AbstractBuilder
{

    use Paginated;

    /**
     * @param $name
     * @param $arguments
     *
     * @return \SzuniSoft\Unas\Internal\Builders\GetOrderBuilder
     */
    public function __call($name, $arguments)
    {
        /** @var mixed|Carbon $arg */
        $arg = $arguments[0];

        switch ($name) {
            case 'timeStart':
            case 'timeEnd':
            case 'timeModStart':
            case 'timeModEnd':
                $arg = $arg->timestamp;
                break;

            case 'dateStart':
            case 'dateEnd':
                $arg = $arg->format(ApiSchema::COMMON_DATE_FORMAT);
                break;

            case 'status':
            case 'statusKey':
            case 'invoiceStatus':
            case 'invoiceAutoSet':
            case 'limitStart':
            case 'limitNum':
            case 'key':
                break;
            default:
                throw new InvalidBuilderMethodException($name, self::class);
        }

        parent::__call($name, $arg);

        return $this;
    }

}
