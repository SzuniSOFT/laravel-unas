<?php


namespace SzuniSoft\Unas\Model\Support;


use Illuminate\Database\Eloquent\Concerns\HasAttributes;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use function in_array;
use function is_array;

/**
 * Class AbstractModel
 * @package SzuniSoft\Unas\Model
 *
 *
 */
abstract class AbstractModel
{

    use HasAttributes;

    /**
     * @var bool
     */
    protected $isNew = true;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->isNew;
    }

    /**
     * @var null|array
     */
    protected $attributesForUpdate = null;

    /**
     * @var null|array
     */
    protected $attributesForCreate = null;

    /**
     * @var array
     */
    protected $ignored = [];

    /**
     * Order constructor.
     *
     * @param array $raw
     * @param bool  $exists
     */
    public function __construct(array $raw, $exists = false)
    {
        $this->isNew = !$exists;

        foreach ($raw as $key => $value) {

            $key = Str::snake($key);

            if (in_array($key, $this->ignored)) {
                continue;
            }

            // Skip nested arrays.
            // Let descendants handle it.
            if (is_array($value)) {
                continue;
            }

            $this->setAttribute($key, $value);
            $this->syncOriginal();
        }
    }

    /**
     * @return array
     */
    public function getAttributesForUpdate()
    {
        $changes = $this->syncChanges()->getChanges();
        if ($this->attributesForUpdate) {
            $changes = Arr::only($changes, $this->attributesForUpdate);
        }
        return $changes;
    }

    /**
     * @return array
     */
    public function getAttributesForCreate()
    {
        $attributes = $this->attributesToArray();
        if ($this->attributesForCreate) {
            $attributes = Arr::only($attributes, $this->attributesForCreate);
        }
        return $attributes;
    }

    /**
     * @return array
     * @codeCoverageIgnore
     */
    public static function commonValidationRules()
    {
        return [];
    }

    /**
     * @return array
     * @codeCoverageIgnore
     */
    public function getDates()
    {
        return [];
    }

    /**
     * Get the value indicating whether the IDs are incrementing.
     * @codeCoverageIgnore
     * @return bool
     */
    public function getIncrementing()
    {
        return false;
    }

    /**
     * @param $key
     * @codeCoverageIgnore
     * @return null
     */
    public function getRelationValue($key)
    {
        return null;
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->getAttribute($name);
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->setAttribute($name, $value);
    }

    /**
     * @param array $src
     *
     * @return \Illuminate\Support\Collection
     */
    protected function collection(array $src)
    {
        if (Arr::isAssoc($src)) {
            return Collection::wrap([$src]);
        }
        return Collection::wrap($src);
    }
}
