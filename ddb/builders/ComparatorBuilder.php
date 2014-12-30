<?php
namespace dnocode\awsddb\ddb\builders;

use Aws\CloudWatch\Enum\ComparisonOperator;
use Aws\DynamoDb\Enum\Type;
use Aws\ImportExport\Exception\InvalidParameterException;
use ComparatorBuilder\ConditionsBuilder;
use dnocode\awsddb\ddb\inputs\AttrValueCondition;
use yii\base\Object;
use yii\helpers\ArrayHelper;

class ComparatorBuilder extends Object{

    /**util properties*/
    protected $_current_key;
    /***Builder properties****/
    /** @var  ConditionsBuilder $_conditionsBuilder */
    protected $_conditionsBuilder;
    /**Output properties**/
    protected $_attr_values_conditions=array();

    public $cond_choosen="AND";


    public function andd($attributeName=""){

        return $this->addAttribute($attributeName,"AND");
    }

    public function orr($attributeName=""){

            return $this->addAttribute($attributeName,"OR");
    }

    /**
     * @param string $name
     * @return ConditionsBuilder
     * @throws \Aws\ImportExport\Exception\InvalidParameterException
     */
    private function addAttribute($attributeName="",$conditionalOperator="AND"){

        if(count($attributeName)==0){throw new InvalidParameterException("name is required!!");}
        $this->_conditionsBuilder=$this->_conditionsBuilder==null?new ConditionsBuilder():$this->_conditionsBuilder;
        $this->_current_key=$attributeName;
        /** there isn`t  AttrValueCondition for this attribute create it **/
        if(array_key_exists($attributeName,$this->_attr_values_conditions)==false){
        $this->_attr_values_conditions[$attributeName]=new AttrValueCondition();
        $this->_attr_values_conditions[$attributeName]->name=$attributeName;
        }

        $currentAttrValueCondition=$this->current();
        $this->_conditionsBuilder->set($this,$currentAttrValueCondition);
        $this->cond_choosen=$conditionalOperator;
        return  $this->_conditionsBuilder ;

    }

    /**
     * @throws NotSupportedException
     */
    public function all(){


        throw new NotSupportedException(__METHOD__ . ' is not supported.');

    }

    /**
     * @throws NotSupportedException
     */
    public function one(){   throw new NotSupportedException(__METHOD__ . ' is not supported.');}

    /**
     * @return AttrValueCondition
     */
    private function current(){return $this->_attr_values_conditions[$this->_current_key];}

    /**
     * @param AttrValueCondition $attribute
     */
    public function injectAttribute($attribute){

        $this->_attr_values_conditions[$attribute->name]=$attribute;
    }



    public function getAttribute($attributeName){

        if(!array_key_exists($attributeName,$this->_attr_values_conditions)){
            throw new InvalidParameterException("attribute: $attributeName not found");
        }

        return   $this->_attr_values_conditions [$attributeName];
    }





        public function columns(){

        $columns=array();
        /** @var AttrValueCondition $attr */
        foreach($this->_attr_values_conditions as $key=>$attr){
            $columns[$attr->name]=null;

        }
            return $columns;
    }

    public function toArray($get=false){

        $output=[];

        /** @var AttrValueCondition $attrCond */
        foreach($this->_attr_values_conditions as $attrCond){

            $output=array_merge($output,$attrCond->toArray($get));

        }

        return $output;

    }





}

namespace ComparatorBuilder;
use Aws\ImportExport\Exception\InvalidParameterException;
use yii\base\Object;
use Aws\DynamoDb\Enum\Type;
use dnocode\awsddb\ddb\builders\ComparatorBuilder;
use dnocode\awsddb\ddb\inputs\AttrValueCondition;

class ConditionsBuilder extends Object{

    /** @var  ComparatorBuilder $_cb */
    private $_cb;
    /** @var  AttrValueCondition $current */
    private $_current;


    /**
     * @param ComparatorBuilder $cb
     * @param AttrValueCondition $currentElement
     */
    public function set($cb,&$currentElement){
        $this->_cb=$cb;
        $this->_current=$currentElement;
    }


    /**
     * @param ComparatorBuilder $cb
     * @param $currentElement
     * @param $value
     * @param Type $type
     */
    public function eq($value,$type=null){

        /**wanna onother value to current valuecondition comparison operator will change in IN)**/
        if($this->_cb->cond_choosen==="OR"&&
            $this->_current->count()>0){
            $this->_current->comparison_operator=\Aws\DynamoDb\Enum\ComparisonOperator::IN;

        }else{

            $this->_current->comparison_operator=\Aws\DynamoDb\Enum\ComparisonOperator::EQ;
        }

        $this->_current->add($value,$type);

        return $this->_cb;
    }


    public function gt($value,$type=null){

        $this->_current->add($value,$type);
        $this->_current->comparison_operator=\Aws\DynamoDb\Enum\ComparisonOperator::GT;
        return $this->_cb;
    }

    public function ge($value,$type=null){

        $this->_current->add($value,$type);
        $this->_current->comparison_operator=\Aws\DynamoDb\Enum\ComparisonOperator::GE;
        return $this->_cb;
    }


    public function le($value,$type=null){

        $this->_current->add($value,$type);
        $this->_current->comparison_operator=\Aws\DynamoDb\Enum\ComparisonOperator::LE;
        return $this->_cb;
    }

    public function lt($value,$type=null){

        $this->_current->add($value,$type);
        $this->_current->comparison_operator=\Aws\DynamoDb\Enum\ComparisonOperator::LT;
        return $this->_cb;
    }

    public function contains($value,$type=null){

        $this->_current->add($value,$type);
        $this->_current->comparison_operator=\Aws\DynamoDb\Enum\ComparisonOperator::CONTAINS;
        return $this->_cb;
    }

    public function in($value=array(),$type=null){

        if(is_array($value)==false){throw new InvalidParameterException("value must be a array");}

        foreach($value as $v){ $this->_current->add($v,$type);}

        $this->_current->comparison_operator=\Aws\DynamoDb\Enum\ComparisonOperator::IN;

       return $this->_cb;
    }

}


