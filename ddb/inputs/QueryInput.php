<?php
/**
 * Created by PhpStorm.
 * User: dino
 * Date: 12/16/14
 * Time: 7:06 PM
 */

namespace dnocode\awsddb\ddb\inputs;


use Aws\DynamoDb\Enum\Select;
use Aws\Lambda\LambdaClient;
use dnocode\awsddb\ddb\enums\Filter;
use yii\base\Object;

class QueryInput extends AWSInput {

    /** @var AWSFilter $ScanFilter**/
    protected $_keyConditionsFilter;
    protected $_queryFilter;
    /**
     * @return AWSFilter
     */
    public function filter()
    {
        return $this->_keyConditionsFilter=$this->_keyConditionsFilter==null?new AWSFilter(Filter::KeyConditions):$this->_keyConditionsFilter;
    }


    public function queryFilter(){
    /**query filter  contain attributes that  is not primary key **/
        return $this->_queryFilter=$this->_queryFilter==null?new AWSFilter(Filter::Query):$this->_queryFilter;

    }
}