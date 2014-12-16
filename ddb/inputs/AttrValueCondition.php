<?php
/**
 * Created by PhpStorm.
 * User: dino
 * Date: 12/16/14
 * Time: 10:05 PM
 */
namespace dnocode\awsddb\ddb\inputs;


use app\models\Element;
use Aws\DynamoDb\Enum\AttributeAction;
use Aws\DynamoDb\Model\Attribute;
use Aws\DynamoDb\Model\Item;

class AttrValueCondition {

    const attributesKeys="AttributeValueList";
    const ComparisonOperator="ComparisonOperator";

    public $name;
    private $_value_list=[];
    public $comparison_operator;

    public function add($value,$type=""){

        $attribute=count($type)>0?new Attribute($value,$type):  Attribute::factory($value);
        $this->_value_list[]=$attribute;
    }


    /**
     * return the object in this form
     *
     * 'AttributeValueList' => array(
     *          array('S' => 'overflow')
     *      ),
     *     'ComparisonOperator' => 'CONTAINS'
     *      ),
     *
     *  if the name is indicated
     *
     *  'time' => array(
     *       'AttributeValueList' => array(
     *           array('N' => strtotime('-15 minutes'))
     *          ),
     *           'ComparisonOperator' => 'GT'
     *       )
     **/

    public function toArray(){
            /**todo**/

    }

}