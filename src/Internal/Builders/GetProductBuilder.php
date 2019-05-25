<?php


namespace SzuniSoft\Unas\Internal\Builders;


use Carbon\Carbon;
use SzuniSoft\Unas\Exceptions\InvalidBuilderMethodException;
use SzuniSoft\Unas\Internal\ApiSchema;

/**
 * Class GetProductBuilder
 * @package SzuniSoft\Unas\Internal\Builders
 *
 * @method $this statusBase($status)
 * @method $this id($id)
 * @method $this sku($sku)
 * @method $this parent($parent)
 * @method $this timeStart(Carbon $dateTime)
 * @method $this timeEnd(Carbon $dateTime)
 * @method $this dateStart(Carbon $date)
 * @method $this contentType($level)
 * @method $this contentParam($params)
 * @method $this dateEnd(Carbon $date)
 * @method $this limitStart(int $from)
 * @method $this limitNum(int $limit)
 */
class GetProductBuilder extends AbstractBuilder
{

    use Paginated;

    /**
     * @param $name
     * @param $arguments
     *
     * @return \SzuniSoft\Unas\Internal\Builders\GetProductBuilder
     */
    public function __call($name, $arguments)
    {

        /** @var mixed|\Carbon\Carbon $arg */
        $arg = $arguments[0];

        switch ($name) {
            case 'timeStart':
            case 'timeEnd':
                $arg = $arg->timestamp;
                break;

            case 'dateStart':
            case 'dateEnd':
                $arg = $arg->format(ApiSchema::COMMON_DATE_FORMAT);
                break;

            case 'statusBase':
            case 'id':
            case 'sku':
            case 'parent':
            case 'contentType':
            case 'contentParam':
            case 'limitStart':
            case 'limitNum':
                break;
            default:
                throw new InvalidBuilderMethodException($name, self::class);
        }

        parent::__call($name, $arg);

        return $this;
    }

}
