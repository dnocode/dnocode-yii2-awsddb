<?php
/**
 * Created by PhpStorm.
 * User: dino
 * Date: 12/16/14
 * Time: 9:41 PM
 */

namespace dnocode\awsddb\ddb\inputs;


use Aws\DynamoDb\Enum\Select;
use Aws\DynamoDb\Model\Item;
use dnocode\awsddb\ddb\enums\Search;
use yii\debug\components\search\Filter;

abstract class AWSInput {

    protected  $_tablename;
    /** @var  string $indexName index choice name */
    protected $_indexname;

    protected $_consistent_read=true;
    /** @var  integer $limit */
    protected $_limit;
    /** @var   Select | array $_select */
    protected $_select;
    /** @var attributes list */
    protected  $_attributes_get=[];
    /** @var  Item  to insert */
    protected $_modelItem;



    /**
     * @param $tablename
     * @return AWSInput $this
     */
    public function consistent($value=true){

        $this->_consistent_read=$value;
       return $this;

    }
        /**
     * @param $tablename
     * @return AWSInput $this
     */
    public function select($attributes=[]){

        if(empty($attributes)){
            $this->_select=empty($this->_indexname)?Select::ALL_ATTRIBUTES:Select::ALL_PROJECTED_ATTRIBUTES;
            return $this;
        }
        $this->_select=Select::SPECIFIC_ATTRIBUTES;
        $this->_attributes_get=$attributes;
        return $this;
    }


    public function count(){

        $this->_select=Select::COUNT;
        return $this;
    }


    /**
     * @param $tablename
     * @return AWSInput $this
     */
    public function tableName($tablename){
    $this->_tablename=$tablename;
        return $this;
    }

    /**
     * @param $indexName
     * @return AWSInput $this
     */
    public function indexName($indexName){
    $this->_indexname=$indexName;
        return $this;
    }

    /**
     * @param $number
     * @return AWSInput $this
     */
    public function limit($number){
        $this->_limit=$number;
        return $this;
    }


    /**
     * @return AWSFilter
     */
    public abstract function filter();




    public function toArray(){

        $output=[];
        $output["TableName"]=$this->_tablename;
        if($this->filter()!=null)
        $output=array_merge($output,$this->filter()->toArray());
        if(!empty($this->_attributes_get))
        $output["AttributesToGet"]=$this->_attributes_get;
        if(!empty($this->_consistent_read))
        $output["ConsistentRead"]=$this->_consistent_read;
        if(!empty($this->_limit))
        $output["Limit"]=$this->_limit;
        if($this->filter()!=null&&count($output[$this->filter()->filter_type])>1 and $this->filter()->filter_type!==\dnocode\awsddb\ddb\enums\Filter::Key)
        $output["ConditionalOperator"]=$this->filter()->conditionalOperator();
        if($this->_modelItem!=null){ $output["Item"]=$this->_modelItem->toArray(); }

        return $output;
    }



} 