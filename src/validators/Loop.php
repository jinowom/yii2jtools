<?php
namespace jinowom\yii2jtools\validators;


use yii\db\ActiveRecord;
use yii\validators\Validator;

/**
 * Class Loop
 * @desc 回环验证
 */
class Loop extends Validator
{
    public $parentForAttribute = 'id';
    public $parentModelLinkname = 'p';

    /**
     * @param ActiveRecord $model
     * @param string $attribute
     */
    public function validateAttribute($model, $attribute)
    {
        $parent_for_attribute = $this->parentForAttribute;
        $search = $model->$parent_for_attribute;
        if ($search == $model->$attribute){
            $bool = true;
        }else{
            /**
             * @var ActiveRecord $class_name
             */
            $class_name = get_class($model);
            $parent = $class_name::findOne([$parent_for_attribute => $model->$attribute]);
            $bool = $this->validateSearch($search, $parent, $attribute);
        }
        if ($bool){
            $this->addError($model, $attribute, $attribute . '产生回环');
        }
    }

    /**
     * @param string $search
     * @param ActiveRecord $parent
     * @return bool
     */
    protected function validateSearch($search, $parent, $attribute)
    {
        $parent_model_linkname = $this->parentModelLinkname;
        if ($parent){
            if ($parent->$attribute == $search){
                return true;
            }else{
                return $this->validateSearch($search, $parent->$parent_model_linkname, $attribute);
            }
        }else{
            return false;
        }
    }
}