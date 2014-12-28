<?php
/**
 * Created by PhpStorm.
 * User: dino
 * Date: 12/16/14
 * Time: 7:06 PM
 */

namespace dnocode\awsddb\ddb\inputs;


use Aws\DynamoDb\Enum\Select;
use dnocode\awsddb\ddb\enums\Filter;
use yii\base\Object;

class ScanInput extends AWSInput {

    /** @var AWSFilter $ScanFilter**/
    protected $_scanFilter;

    public function filter(){

        return $this->_scanFilter=$this->_scanFilter==null?new AWSFilter(Filter::ScanFilter):$this->_scanFilter;
    }






} 