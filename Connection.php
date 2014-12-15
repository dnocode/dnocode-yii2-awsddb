<?php

namespace dnocode\awsddb;

use Aws\DynamoDb\DynamoDbClient;
use dnocode\awsddb\Command;
use yii\base\Component;
use Aws\DynamoDb\Enum\AttributeAction;


/**
 * The ddb connection class is used to establish a connection to a DynamoDbServer
 *
 * By default it assumes there is a dynamo db  server running on localhost at port 8000 and uses the database number 0.
 *
 *
 * @property boolean $isActive Whether the DB connection is established. This property is read-only.
 * 
 *
 * @author Dino Ricceri <ricceri.dino@gmail.com>
 * @since 2.0
 */
class Connection extends Component
{
    /**
     * @event Event an event that is triggered after a DB connection is established
     */
    const EVENT_AFTER_OPEN = 'afterOpen';
    public $key;
    public $secret;
    public $region;
    public $base_url;
    /*** @var QueryBuilder*/
    private $_builder;
    /*** @var DynamoDbClient*/
    private $_amzclient;
    private $_transactions;
    /**
     * Initializes the DB connection.
     * This method is invoked right after the DB connection is established.
     * The default implementation triggers an [[EVENT_AFTER_OPEN]] event.
     */
    public function init()
    {

        $config=array(
            'key'    => $this->key,
            'secret' => $this->secret,
            'region' => $this->region
        );

        $config=$this->base_url!=null?$config["base_url"]=$this->base_url:$config;

        $this->_amzclient=DynamoDbClient::factory($config);


        $this->trigger(self::EVENT_AFTER_OPEN);
    }

    public function aws(){return $this->_amzclient;}

    public function getCommandBuilder(){

        if ($this->_builder === null) {

            $this->_builder = new CommandBuilder($this);
        }
        return $this->_builder;
    }



    public function createQueryCommand($query,$params=array()){

        return  $this->createTransactionInternal($query,null,"QUERY",null,$params);

    }

    public function createCommand($tablename,$attributeAction,$attributes=array(),$params=array()){

        return  $this->createTransactionInternal(null,$tablename,$attributeAction,$attributes,$params);

    }


    /**
     * @param $query
     * @param $tablename
     * @param $attributeAction
     * @param array $attributes
     * @param array $params
     * @return Transacetion
     */
     function createTransactionInternal($query,$tablename,$attributeAction,$attributes,$params)
    {
        /** @var  Transact $transaction */
       $transactionExist=$this->beginTransaction($tablename);

       $transaction=$this->getTransaction($tablename);

        $config=['table'=>$tablename,
                 'attributes'=>$attributes,
                 'params'=>$params,
                  ];

        switch($attributeAction){

            case AttributeAction::PUT:

                /**create command insert **/
                $commandClassName='dnocode\awsddb\PutCommand';
                break;

            case AttributeAction::DELETE:
                $commandClassName='dnocode\awsddb\DelCommand';
                break;
            default:
                /**query Command**/
                $commandClassName='dnocode\awsddb\GetCommand';
        }

        $config["class"]=$commandClassName;

        $cmd=  $this->getCommandBuilder()->build($query,$config,$params);

        $transaction->addCommand($cmd);

        if(!$transactionExist){ $transaction->commit();}

        return $transaction;

    }


    /**
     * return true if the transaction already existed
     * return false if the transaction has been created
     * @param $tablename
     * @return bool
     */
    public function beginTransaction($uid){

        /** @var  Transact $transaction */
        $transactionExist=( count($this->_transactions)>0&&array_key_exists($uid,$this->_transactions));

        $transactionExist?($this->_transactions[$uid]):($this->_transactions[$uid]=new Transact(['db'=>$this,"uid"=>$uid]));

        return $transactionExist;

    }


    public function removeTransaction($uid){unset($this->_transactions[$uid]);}


    public function getTransaction($uid){ return array($uid,$this->_transactions)?$this->_transactions[$uid]:null;}


}
