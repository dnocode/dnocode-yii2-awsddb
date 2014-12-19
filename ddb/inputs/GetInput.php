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

class GetInput extends AWSInput {

    /** @var AWSFilter $_key_filter **/
    protected $_key_filter;

    public function filter(){

        return $this->_key_filter=$this->_key_filter==null?new AWSFilter(Filter::Key):$this->_key_filter;
    }

} 