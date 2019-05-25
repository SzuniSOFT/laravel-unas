<?php


namespace SzuniSoft\Unas\Model;


use SzuniSoft\Unas\Model\Support\AbstractModel;

/**
 * Class ProductImage
 * @package SzuniSoft\Unas\Model
 *
 * @property string $type
 * @property string $id
 * @property string $small_url
 * @property string $medium_url
 * @property string $big_url
 */
class ProductImage extends AbstractModel
{

    public function __construct(array $raw, bool $exists = false)
    {
        parent::__construct($raw, $exists);

        if (isset($raw['Url']) && isset($raw['Url']['Small'])) {
            $this->small_url = $raw['Url']['Small'];
        }

        if (isset($raw['Url']) && isset($raw['Url']['Medium'])) {
            $this->medium_url = $raw['Url']['Medium'];
        }

        if (isset($raw['Url']) && isset($raw['Url']['Big'])) {
            $this->big_url = $raw['Url']['Big'];
        }
    }
}
