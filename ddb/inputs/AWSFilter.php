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
    private $_filter_type;


    /**
     * @param Filter $filter_type
     */
    public function __construct($filter_type){$this->_filter_type=$filter_type;}

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


    /**
     * @param Search $what
     */
    function toArray($what){

        /**
         *

         *          'ScanFilter' => array(
        '                                   error' => array(
                                                                  'AttributeValueList' => array(
                                                                                    array('S' => 'overflow')
                                                                                                ),
                                                                                        'ComparisonOperator' => 'CONTAINS'
                                                                                                ),
                                                                                    'time' => array(
                                                                                     'AttributeValueList' => array(
                                                                  array('N' => strtotime('-15 minutes'))
                                                                         ),
                                                                     'ComparisonOperator' => 'GT'
                                                        )

         */
    }

}

