<?php
namespace common\models\admindata;

use Yii;
use common\models\admindatabase\AuthAssignment as BaseModelAuthAssignment;
use yii\caching\TagDependency;

/**
* This is the data class for [[common\models\admindatabase\AuthAssignment]].
* Data model definde model behavior and status code.
* @see \common\models\admindatabase\AuthAssignment
*/
class AuthAssignment extends BaseModelAuthAssignment
{
    /**
     * The cache tag
     */
    const CACHE_TAG='common_models_admindata_AuthAssignment';


    /**
     * get status code attribute list
     */
    public static function statusCodes(){
        return [
        ];
    }

    /**
    * @inheritdoc
    */
    public function rules()
    {
        return array_merge(parent::rules(),[
        ]);
    }

    /**
    * @inheritdoc
    */
    public function scenarios()
    {
        return [
            'default' => [
                'item_name',
                'user_id',
                'created_at',
            ],
            'search' => [
                'item_name',
                'user_id',
                'created_at',
            ],
            'view' => [
                'item_name',
                'user_id',
                'created_at',
            ],
            'update' => [
                'item_name',
                'user_id',
            ],
            'create' => [
                'item_name',
                'user_id',
            ],
        ];
    }

    public function behaviors()
    {
        return [
            'timeUpdate'=>[
                'class' => \yii\behaviors\TimestampBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['created_at'],
                ],
            ],
//            'withOneUser'=>[
//                'class' => \lbmzorx\components\behavior\WithOneUser::className(),
//                'userClass'=> User::ClassName(),
//            ],
        ];
    }



    /**
     * If is tree which have parent_id
     * @return boolean
     */
    public static function isTree(){
        return false;
    }


    public function afterSave($insert , $changedAttributes)
    {
        TagDependency::invalidate(\yii::$app->cache,self::CACHE_TAG);
        parent::afterSave($insert , $changedAttributes); // TODO: Change the autogenerated stub
    }

    public function afterDelete()
    {
        TagDependency::invalidate(\yii::$app->cache,self::CACHE_TAG);
        parent::afterDelete(); // TODO: Change the autogenerated stub
    }

}
