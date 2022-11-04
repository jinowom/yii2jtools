<?php
namespace jinowom\yii2jwtools\validators;


use yii\validators\Validator;

class ArrayValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        if (!is_array($model->$attribute)){
            $this->addError($model, $attribute, $attribute . '必须是一个数组');
        }
    }
}