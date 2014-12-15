<?php
/**
 * Created by PhpStorm.
 * User: dino
 * Date: 12/11/14
 * Time: 5:59 PM
 */

namespace dnocode\awsddb;

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
        /** @var Command $cmd  */
        foreach(  $this->_commands as $uid=>$cmd ){
            $cmd->doIt();
            $this->last_executed=$cmd;
        }

        Yii::info("transaction   uid: $this->uid committed");

        $this->db->removeTransaction($this->uid);
        Yii::info("transaction   uid: $this->uid removed");
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