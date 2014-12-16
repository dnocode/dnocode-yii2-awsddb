<?php
/**
 * Created by PhpStorm.
 * User: dino
 * Date: 12/16/14
 * Time: 9:54 PM
 */

namespace dnocode\ddb\builders;


use Aws\CloudWatch\Enum\ComparisonOperator;
use Aws\ImportExport\Exception\InvalidParameterException;
use dnocode\awsddb\ddb\inputs\AttrValueCondition;
use yii\base\Object;
use yii\helpers\ArrayHelper;

class ComparatorBuilder extends Object{

    private $_current_key;
    /**
     * @var array
     */
    private $_attr_values_conditions=array();

    /**
     * util for add attributes value condition
     * @param string $name
     */
    public function addAttr($name=""){

          if(count($name)==0){throw new InvalidParameterException("name is required!!");}
           $this->_current_key=$name;
           $this->_attr_values_conditions[$name]=new AttrValueCondition();
    }

    public function eq($value,$type=""){

          $this->current()->add($value,$type);
          $this->current()->comparison_operator=\Aws\DynamoDb\Enum\ComparisonOperator::EQ;
    }


    /**
     * @return AttrValueCondition
     */
    private function current(){return $this->_attr_values_conditions[$this->_current_key];}





} 