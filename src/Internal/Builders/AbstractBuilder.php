<?php


namespace SzuniSoft\Unas\Internal\Builders;


use Illuminate\Support\Collection;

/**
 * Class AbstractBuilder
 * @package SzuniSoft\Unas\Internal\Builders
 * @codeCoverageIgnore
 */
abstract class AbstractBuilder
{

    /**
     * @var callable
     */
    private $cb;

    /**
     * @var array
     */
    private $params = [];

    public function __construct(callable $cb)
    {
        $this->cb = $cb;
    }

    /**
     * @param callable $cb
     *
     * @return static
     */
    public static function make(callable $cb)
    {
        return new static($cb);
    }

    /**
     * @param $name
     * @param $arguments
     */
    public function __call($name, $arguments)
    {
        $this->params[$name] = $arguments[0];
    }

    /**
     * @return Collection
     */
    public function retrieve()
    {
        $cb = $this->cb;
        return $cb($this->params);
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

}
