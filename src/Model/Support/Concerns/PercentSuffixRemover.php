<?php


namespace SzuniSoft\Unas\Model\Support\Concerns;


use Illuminate\Support\Str;

/**
 * Trait PercentSuffixRemover
 * @package SzuniSoft\Unas\Model\Support\Concerns
 */
trait PercentSuffixRemover
{

    /**
     * @param $value
     *
     * @return float
     */
    protected function removePercentSuffix($value)
    {
        if (!$value) {
            return $value;
        }

        if (Str::endsWith($value, '%')) {
            $value = str_replace('%', '', $value);
        };

        return (float)$value;
    }

}
