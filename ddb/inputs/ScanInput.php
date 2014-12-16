<?php
/**
 * Created by PhpStorm.
 * User: dino
 * Date: 12/16/14
 * Time: 7:06 PM
 */

namespace dnocode\awsddb\ddb\inputs;

use dnocode\awsddb\ddb\inputs\AWSFilter;
use Aws\DynamoDb\Enum\Select;
use yii\base\Object;

class ScanInput extends AWSFilter {

    /** @var AWSFilter $ScanFilter**/
    public $_scanFilter;


    public function filter(){


        return $this->_scanFilter=$this->_scanFilter==null?new AWSFilter();

    }

} 