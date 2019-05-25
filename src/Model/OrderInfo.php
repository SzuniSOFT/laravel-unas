<?php


namespace SzuniSoft\Unas\Model;


use Illuminate\Database\Eloquent\Concerns\HasAttributes;
use Illuminate\Support\Collection;

/**
 * Class OrderInfo
 * @package SzuniSoft\Unas\Model
 *
 * @property Collection $merged_from
 * @property Collection $separated_to
 * @property Collection $separated_from
 */
class OrderInfo
{

    use HasAttributes;

    public function __construct(array $raw)
    {
        if (isset($raw['MergedFrom']) && isset($raw['MergedFrom']['Key'])) {
            $this->merged_from = Collection::wrap($raw['MergedFrom']['Key']);
        }

        if (isset($raw['SeparatedTo']) && isset($raw['SeparatedTo']['Key'])) {
            $this->separated_to = Collection::wrap($raw['SeparatedTo']['Key']);
        }

        if (isset($raw['SeparatedFrom']) && isset($raw['SeparatedFrom']['Key'])) {
            $this->separated_from = Collection::wrap($raw['SeparatedFrom']['Key']);
        }
    }

}
