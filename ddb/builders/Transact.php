<?php
/**
 * Created by PhpStorm.
 * User: dino
 * Date: 12/11/14
 * Time: 5:59 PM
 */

namespace dnocode\ddb\builders;

use Aws\ImportExport\Exception\InvalidParameterException;
use Yii;
use yii\base\InvalidConfigException;

class Transact extends \yii\base\Object {

  public $uid;
  /* @var Connection the database connection that this transaction is associated with.*/
  public $db;
  private $_commands=array();
  public $last_executed;

    /**
     * Commits a transaction.
     * @throws Exception if the transaction is not active
     */
    public function commit()
    {
        Yii::info("committing transaction uid: $this->uid");

        /**unique commands**/
       if(count($this->_commands)==1){

            $cmd=reset($this->_commands);
            $cmd->doIt();
            $this->last_executed=$cmd;

       }else{

           //todo
           $this->createAndExecuteMasterCommands();
       }

        Yii::info("transaction   uid: $this->uid committed");

        $this->db->removeTransaction($this->uid);

        Yii::info("transaction   uid: $this->uid removed");
    }



     public function getResult(){

         if(!$this->last_executed){return false;}

         /** @var Command $cmd */
         $cmd=$this->last_executed;

         $items=$cmd->result->toArray();

         return $items["Items"];

     }



    /**
     * todo
     * this method will divide the commands in write commands [put and delete]
     * and get commands (query)
     * then will be creates  a master commands with the amazon input as
     * you can see at @http://docs.aws.amazon.com/aws-sdk-php/latest/class-Aws.DynamoDb.DynamoDbClient.html#_batchWriteItem
     * one for the write requests
     * one for the query requests
     * the last one will be store in the last command of the transaction
     */
    private function createAndExecuteMasterCommands(){

        $amz_input=array();
        /** @var Command $cmd   more commands different nature */
        /* foreach(  $this->_commands as $uid=>$cmd ){
         }*/
    }

    public function length(){ return count($this->_commands); }

    /* @param Command $cmd */
    public function addCommand($cmd){

        if($cmd->uid==null){$cmd->uid="cmd-".count($this->_commands);}

        if(array_key_exists($cmd->uid,$this->_commands)){

           Yii::error("uid $cmd->uid already exist");
           throw new InvalidParameterException;
       }
      $this->_commands[$cmd->uid]=$cmd;
    }

    public function set_db($db){ $this->_db=$db;}

} 