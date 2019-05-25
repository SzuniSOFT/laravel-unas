<?php


namespace SzuniSoft\Unas\Model\Support;


use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

abstract class AbstractAncestorModel extends AbstractModel
{

    abstract protected function validationRulesForUpdate();

    abstract protected function validationRulesForCreate();

    abstract protected function optimizeForUpdate();

    abstract protected function optimizeForCreate();

    /**
     * @return mixed
     * @throws ValidationException
     */
    public function getForUpdate()
    {
        $v = Validator::make($data = $this->optimizeForUpdate(), $this->validationRulesForUpdate());
        if ($v->fails()) {
            throw new ValidationException($v);
        }
        return $data;
    }

    /**
     * @return mixed
     * @throws ValidationException
     */
    public function getForCreate()
    {
        $v = Validator::make($data = $this->optimizeForCreate(), $this->validationRulesForCreate());
        if ($v->fails()) {
            throw new ValidationException($v);
        }
        return $data;
    }
}
