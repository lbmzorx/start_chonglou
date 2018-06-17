<?php
namespace common\models\admindata;

use Yii;
use common\models\admindatabase\UserMessageGroupContent as BaseModelUserMessageGroupContent;
use yii\caching\TagDependency;

/**
* This is the data class for [[common\models\admindatabase\UserMessageGroupContent]].
* Data model definde model behavior and status code.
* @see \common\models\admindatabase\UserMessageGroupContent
*/
class UserMessageGroupContent extends BaseModelUserMessageGroupContent
{
    /**
     * The cache tag
     */
    const CACHE_TAG='common_models_admindata_UserMessageGroupContent';


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
                'id',
                'content',
            ],
            'search' => [
                'id',
                'content',
            ],
            'view' => [
                'id',
                'content',
            ],
            'update' => [
                'content',
            ],
            'create' => [
                'content',
            ],
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
