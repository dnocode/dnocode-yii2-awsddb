<?php
/**
 * Created by PhpStorm.
 * User: dino
 * Date: 12/16/14
 * Time: 7:06 PM
 */

namespace dnocode\awsddb\ddb\inputs;


use Aws\DynamoDb\Enum\Select;
use yii\base\Object;

class PutInput extends AWSInput {


    /**
     * @return AWSFilter
     */
    public function filter()
    {
        // TODO: Implement filter() method.
    }
}