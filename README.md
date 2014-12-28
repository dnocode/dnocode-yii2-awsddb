Amazon Dynamodb activeRecord Yii 2
===============================================
This extension provides activeRecord support for amazon dynamo db

return [
    //....
    'components' => [
        ddb' =>  [
                   "class"=>'dnocode\awsddb\ar\Connection',
                   'base_url'=>"http://localhost:8000 [OPTIONAL ONLY FOR DYNAMO LOCAL]",
                   'key'    => 'AMAZONKEY',
                    'secret' => 'AMAZONSECRET',
                    'region' => 'eu-west-1'
                ],
    ]
];


Installation
===============================================

Add to composer dependencies

"dnocode/yii2-awsddb": "*"


USING
===============================================


How to define a model
```
class Element {
   public function attributes(){
        return
            ["uid",
            "name",
            "surname",
            "sex"
            ];
    }
    /**hash and range**/
    public static function primaryKey(){ return ["uid"];}

    public function rules(){    return [[['uid'], 'required']];}
}
```

#put
```
$e=new Element();
$e->name
$e->name="nerd";
$e->surname="iam";
$e->sex="no_nerd_i_said";
$e->uid="ciao";
$e->save();
```
#find and update
```
 $element=Element::find()->where(["uid"=>"ciao"])  ->one();
 $element->surname="update";
 $consumer->save();
```
#delete and update
```
 $element=Element::find()->where(["uid"=>"ciao"])  ->one();
 $element->delete();
 Element::deleteAll(["uid"=>"ciao"]);
```
# find with where
```
  $element=Element::find()->
          where(["surname"=>"iam"])
          ->one();
```

 ## Find object with the hash key
    the active record will use
     get operation 4  performance*/

    ```
    $element=Element::find()->
            andWhere("uid")
            ->eq("ciao")
            ->all();*/
     ```
 ## Execute find on attribute  that isn`t primary key
        will be execute a scan operation with filter on that attribute

    ```
    $element=Element::find()->
            andwhere("surname")->eq("prova")
            ->all();
    ```
 ## Compare   attribute with more than one value
    ```
     $element=Element::orWhere("name")->in(["name1","name2"])
     ->all();
    ```
#TODO
1. batch operations with transaction
2. support for relation
3. iterator for query with more than 1MB




