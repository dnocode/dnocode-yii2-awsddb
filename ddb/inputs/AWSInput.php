<?php
/**
 * Created by PhpStorm.
 * User: dino
 * Date: 12/16/14
 * Time: 9:41 PM
 */

namespace dnocode\awsddb\ddb\inputs;


class AWSInput {

    protected  $_tablename;
    /** @var  string $indexName index choice name */
    protected $_indexname;
    /** @var  integer $limit */
    protected $limit;
    /** @var  Select $Select*/
    protected $_select;
    /** @var attributes list */
    protected  $attributes_get=[];
    /** @var attributes to insert */
    protected  $attributes_put=[];



    public function tableName($tablename){return $this;}
    public function indexName($indexName){return $this;}
    public function limit($number){return $this;}





    /*    // TableName is required
    'TableName' => 'string',
    'IndexName' => 'string',
    'AttributesToGet' => array('string', ... ),
    'Limit' => integer,
    'Select' => 'string',
    'ScanFilter' => array(
        // Associative array of custom 'AttributeName' key names
    'AttributeName' => array(
    'AttributeValueList' => array(
    array(
    'S' => 'string',
    'N' => 'string',
    'B' => 'string',
    'SS' => array('string', ... ),
    'NS' => array('string', ... ),
    'BS' => array('string', ... ),
    'M' => array(
        // Associative array of custom 'AttributeName' key names
    'AttributeName' => array(
        // Associative array of custom key value pairs
    ),
        // ... repeated
    ),
    'L' => array(
    array(
        // Associative array of custom key value pairs
    ),
        // ... repeated
    ),
    'NULL' => true || false,
    'BOOL' => true || false,
    ),
        // ... repeated
    ),
        // ComparisonOperator is required
    'ComparisonOperator' => 'string',
    ),
        // ... repeated
    ),
    'ConditionalOperator' => 'string',
    'ExclusiveStartKey' => array(
        // Associative array of custom 'AttributeName' key names
    'AttributeName' => array(
    'S' => 'string',
    'N' => 'string',
    'B' => 'string',
    'SS' => array('string', ... ),
    'NS' => array('string', ... ),
    'BS' => array('string', ... ),
    'M' => array(
        // Associative array of custom 'AttributeName' key names
    'AttributeName' => array(
        // Associative array of custom key value pairs
    ),
        // ... repeated
    ),
    'L' => array(
    array(
        // Associative array of custom key value pairs
    ),
        // ... repeated
    ),
    'NULL' => true || false,
    'BOOL' => true || false,
    ),
        // ... repeated
    ),
    'ReturnConsumedCapacity' => 'string',
    'TotalSegments' => integer,
    'Segment' => integer,
    'ProjectionExpression' => 'string',
    'FilterExpression' => 'string',
    'ExpressionAttributeNames' => array(
        // Associative array of custom 'ExpressionAttributeNameVariable' key names
    'ExpressionAttributeNameVariable' => 'string',
        // ... repeated
    ),
    'ExpressionAttributeValues' => array(
        // Associative array of custom 'ExpressionAttributeValueVariable' key names
    'ExpressionAttributeValueVariable' => array(
    'S' => 'string',
    'N' => 'string',
    'B' => 'string',
    'SS' => array('string', ... ),
    'NS' => array('string', ... ),
    'BS' => array('string', ... ),
    'M' => array(
        // Associative array of custom 'AttributeName' key names
    'AttributeName' => array(
        // Associative array of custom key value pairs
    ),
        // ... repeated
    ),
    'L' => array(
    array(
        // Associative array of custom key value pairs
    ),
        // ... repeated
    ),
    'NULL' => true || false,
    'BOOL' => true || false,
    ),
        // ... repeated
    ),
    ));*/

} 