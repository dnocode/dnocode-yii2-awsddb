<?php

namespace dnocode\awsddb\test\model;

use dnocode\awsddb\ar\ActiveRecord;

class Element extends ActiveRecord {

    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios['asProperty'] = ['uid','name'];//Scenario Values Only Accepted

        return $scenarios;
    }

    public function relationsMap(){

        return ["element"=>

            ["childs"=> ["scenario"=>"asProperty","target"=>"parent"],

             "parents"=>["scenario"=>"asProperty","target"=>"child"]
            ]
        ];
    }

    public static function primaryKey(){ return ["uid"];}

    public function rules(){    return [[['uid'], 'required']];}

    public $uid;
    public $name;
    public $surname;
    public $sex;
    /** inside table relation properties */
    public $childs=[];
    public $parent;


} 