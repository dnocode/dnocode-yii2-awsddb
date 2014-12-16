<?php
/**
 * Created by PhpStorm.
 * User: dino
 * Date: 12/12/14
 * Time: 11:34 AM
 */

namespace dnocode\awsddb;

/**
 * Class PutCommand
 * @package dnocode\awsddb
 *
 * its equivalent of
 *
 * $result = $client->putItem(array(
    'TableName' => 'errors',
    'Item' => array(
    '   id'      => array('N' => '1201'),
    'time'    => array('N' => $time),
    'error'   => array('S' => 'Executive overflow'),
    'message' => array('S' => 'no vacant areas')
)
));
 */
class PutCommand  extends Command{

    function execute()
    {

        $this->result==$this->aws()->putItem($this->amz_input);

    }

    function toAmazonRequestArray()
    {
        // TODO: Implement toAmazonRequestArray() method.
    }
}