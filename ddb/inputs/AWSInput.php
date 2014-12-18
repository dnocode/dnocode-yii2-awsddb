<?php
/**
 * Created by PhpStorm.
 * User: dino
 * Date: 12/16/14
 * Time: 9:41 PM
 */

namespace dnocode\awsddb\ddb\inputs;


use Aws\DynamoDb\Enum\Select;

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
    /** @var attributes to insert */
    protected  $_attributes_put=[];

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



} 