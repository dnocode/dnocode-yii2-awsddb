<?php
/**
 * Created by PhpStorm.
 * User: dino
 * Date: 1/12/15
 * Time: 8:49 AM
 */

namespace dnocode\awsddb\ddb\enums;


class AttributeType  extends  \Aws\DynamoDb\Enum\AttributeType{

    const M= 'M';
    const L = 'L';
}