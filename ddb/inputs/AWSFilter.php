<?php
/**
 * Created by PhpStorm.
 * User: dino
 * Date: 12/16/14
 * Time: 7:11 PM
 */

namespace dnocode\awsddb\ddb\inputs;


use Aws\DynamoDb\Model\Attribute;
use dnocode\awsddb\ddb\builders\CommandBuilder;
use dnocode\awsddb\enums\Search;
use dnocode\ddb\builders\ComparatorBuilder;

class AWSFilter extends  Object {

    /**
     * this object it used for build
     * the attributes value to use on query or scan as keyconditions || scanfilter
     * @var  ComparatorBuilder $_cB */
    private $_cB;
    private $_filter_type;


    public function __construct($filter_type){


        $this->_filter_type=$filter_type;


    }

    public function attr($name=""){

        $this->_cB=$this->$_cB==null? new ComparatorBuilder():$this->$_cB;
        $this->_cB->add($name);


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

