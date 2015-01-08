<?php

namespace dnocode\awsddb\test\model;

use dnocode\awsddb\ar\ActiveRecord;

class Element extends ActiveRecord {

    public $uid;
    public $name;
    public $surname;
    public $sex;


    public static function primaryKey(){ return ["uid"];}


    public function rules(){    return [[['uid'], 'required']];}


} 