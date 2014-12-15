<?php

namespace dnocode\awsddb;
use Aws\DynamoDb\Enum\ComparisonOperator;

/***
 * Class ComparisonLookup
 * @package dnocode\awsddb
 * query operators translator
 */
class ComparisonLookup  {


  private $_comparison_map=[
      "="=>ComparisonOperator::EQ,
      "<>"=>ComparisonOperator::NE,
      "<="=>ComparisonOperator::LE,
      "<"=>ComparisonOperator::LT,
      ">="=>ComparisonOperator::GE,
      ">"=>ComparisonOperator::GT,
      "is not null"=>ComparisonOperator::NOT_NULL,
      "<>"=>ComparisonOperator::NE,

  ];





}