<?php
/**
 * Created by PhpStorm.
 * User: dino
 * Date: 12/16/14
 * Time: 7:11 PM
 */

namespace dnocode\awsddb\ddb\inputs;



use Aws\DynamoDb\Enum\ComparisonOperator;
use Aws\DynamoDb\Model\Attribute;
use Aws\ImportExport\Exception\InvalidParameterException;
use ComparatorBuilder\ConditionsBuilder;
use dnocode\awsddb\ddb\enums\Filter;
use dnocode\awsddb\ddb\enums\Search;
use dnocode\awsddb\ddb\builders\ComparatorBuilder;
use yii\base\Object;

class AWSFilter extends  Object {

    /**
     * this object it used for build
     * the attributes value to use on query or scan as keyconditions || scanfilter
     * @var  ComparatorBuilder $_cB */
    private $_cB;
    public $filter_type;


    /**
     * @param Filter $filter_type
     */
    public function __construct($filter_type){$this->filter_type=$filter_type;}

    /**
     * @param string $name
     * @return ConditionsBuilder
     */
    public function attr($name=""){

        if(count($name)==0){throw new InvalidParameterException("empty name not allowed!!");}

        $this->_cB=$this->_cB==null? new ComparatorBuilder():$this->_cB;

        return $this->_cB->andd($name);

    }


    /**
     * @param AttrValueCondition $attribute
     */
    public function injectAttribute($attribute,$isPK=false){
        $this->_cB=$this->_cB==null? new ComparatorBuilder():$this->_cB;

        if($isPK)$attribute->comparison_operator=ComparisonOperator::EQ;

        $this->_cB->injectAttribute($attribute);
    }


   public function conditionalOperator(){ return $this->_cB->cond_choosen;}



    public function setConditionalOperator($conditionalOperator){

         $this->_cB->cond_choosen=$conditionalOperator;;
    }




    /**
     * @param Search $what
     */
    function toArray(){

        return $this->_cB===null?array():[$this->filter_type=>$this->_cB->toArray($this->filter_type==Filter::Key)];

    }

}

