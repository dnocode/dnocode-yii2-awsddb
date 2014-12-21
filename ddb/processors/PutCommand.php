<?php
/**
 * Created by PhpStorm.
 * User: dino
 * Date: 12/12/14
 * Time: 11:34 AM
 */

namespace dnocode\awsddb\ddb\processors;
use Aws\DynamoDb\Enum\AttributeAction;

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

    /**
     * @var $type can be putItem | deleteItem | updateItem
     */
    public $type;

    function execute()
    {

        $command="";
        switch($this->type){

            case AttributeAction::PUT:  $command="putItem"; break;
            case AttributeAction::DELETE:  $command="deleteItem"; break;
            case AttributeAction::ADD:  $command="updateItem"; break;
        }

        $this->result==$this->aws()->$command($this->amz_input->toArray($this->type));

    }

    function toAmazonRequestArray()
    {
        // TODO: Implement toAmazonRequestArray() method.
    }
}