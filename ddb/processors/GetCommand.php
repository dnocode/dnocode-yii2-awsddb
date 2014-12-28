<?php
/**
 * Created by PhpStorm.
 * User: dino
 * Date: 12/12/14
 * Time: 11:34 AM
 */

namespace dnocode\awsddb\ddb\processors;

/**
 * Class PutCommand
 * @package dnocode\awsddb
 *
 *  $result = $client->getItem(array(
 *   'ConsistentRead' => true,
 *   'TableName' => 'errors',
 *   'Key'       => array(
 *   'id'   => array('N' => '1201'),
 *   'time' => array('N' => $time)
 *     )
 *    ));
 *     or more complex
 *   ====================================================
 *
 *     $iterator = $client->getIterator('Query', array(
 *     'TableName'     => 'errors',
 *       'KeyConditions' => array(
 *       'id' => array(
 *       'AttributeValueList' => array(
 *       array('N' => '1201')
 *           ),
 *       'ComparisonOperator' => 'EQ'
 *          ),
 *       'time' => array(
 *       'AttributeValueList' => array(
 *       array('N' => strtotime("-15 minutes"))
 *           ),
 *       'ComparisonOperator' => 'GT'
 *       )
 *       )
 *       ));
 *
 *       // Each item will contain the attributes we added
 *       foreach ($iterator as $item) {
 *       // Grab the time number value
 *       echo $item['time']['N'] . "\n";
 *       // Grab the error string value
 *      echo $item['error']['S'] . "\n";
 *       }
 *
 */
class GetCommand extends Command {

    /**
     * @var $type can be scan | get | query
     */
    public $type;


     function execute()
    {
        $method=$this->type;
        $this->result=$this->aws()->$method($this->amz_input->toArray($this->type));

    }

    function toAmazonRequestArray()
    {
        // TODO: Implement toAmazonRequestArray() method.
    }
}