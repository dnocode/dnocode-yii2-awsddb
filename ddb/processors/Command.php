<?php
/**
 * Created by PhpStorm.
 * User: dino
 * Date: 12/11/14
 * Time: 3:41 PM
 */

namespace dnocode\awsddb;

use Aws\CloudFront\Exception\Exception;
use Guzzle\Service\Resource\Model;
use Item;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\base\UnknownPropertyException;


abstract class Command extends Component
{
    /**
     * @var Connection the DB connection that this command is associated with
     */
    public $db;
    /** @var  Model */
    public $result;
    public $amz_input;
    public $params;
    public $uid;

    protected function beforeExecute(){}

    protected function afterExecute(){}


    abstract function execute();

    /**
     * transform a command object in this amazon input
     *
     * 'PutRequest' => array(
    // Item is required
    'Item' => array(
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
     * @return mixed
     */
    abstract function toAmazonRequestArray();

    public function validate(){

        try{
                if($this->db==null){ throw new InvalidConfigException();}

             } catch(Exception $e){ throw $e;}
        }

    public function doIt(){

        $this->beforeExecute();
        Yii::info("executing command uid: $this->uid");
        $this->execute();
        Yii::info("command uid: $this->uid executed" );
        $this->afterExecute();
    }

    public function pullOutOne(){}
    public function pullOutAll(){}

    /**
     * @return \Aws\DynamoDb\DynamoDbClient
     */
    public function aws(){ return $this->db->aws();}




}