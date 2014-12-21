<?php
namespace dnocode\awsddb\ddb\builders;

use dnocode\awsddb\ar\ActiveRecord;
use dnocode\awsddb\ar\Query;
use dnocode\awsddb\ddb\builders\ComparatorBuilder;
use dnocode\awsddb\ddb\inputs\AttrValueCondition;

class QueryComparatorBuilder extends ComparatorBuilder{

    /** @var Query $_query*/
    private $_query;

    function __construct($qry){ $this->_query=$qry;}

    /**
     * @return array|void
     */
    public function all(){
        return $this->_query->all();}

    /**
     * @return ActiveRecord
     */
    public function one(){ return $this->_query->one();}


}