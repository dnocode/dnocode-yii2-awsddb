<?php
/**
 * Created by PhpStorm.
 * User: dino
 * Date: 12/16/14
 * Time: 10:05 PM
 */
namespace dnocode\awsddb\ddb\inputs;




class AttrValueCondition {

    const attributesKeys="AttributeValueList";

    const ComparisonOperator="ComparisonOperator";

    public $name;

    private $_value_list=[];

    public $comparison_operator;

    public function add($value,$type){

        $attribute=count($type)>0?new Attribute($value,$type):  Attribute::factory($value);

        $this->_value_list[]=$attribute;
    }


    public function count(){return count($this->_value_list);}


    /**
     * method return attribute for filter
     * or get operations (attribute_get)
     * @param bool $get
     * @return mixed
     */
    public function toArray($get){

        $output[$this->name]=$get?[]:["AttributeValueList"=>[]];
        /** @var Attribute $attr */
        foreach($this->_value_list as $attr){

           /** if is get operation don`t need AttributeValueList key */
           if($get){ $output[$this->name]=$attr->toArray();
           continue;
           }

           $output[$this->name][AttrValueCondition::attributesKeys][]=$attr->toArray();

        }

        if($get===false)
        $output[$this->name][AttrValueCondition::ComparisonOperator]=$this->comparison_operator;

        return $output;
    }

}