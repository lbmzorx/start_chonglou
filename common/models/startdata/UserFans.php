<?php
namespace common\models\startdata;

use common\components\behavior\UpdataFans;
use Yii;
use common\models\startdatabase\UserFans as BaseModelUserFans;
use yii\base\Event;
use yii\caching\TagDependency;

/**
* This is the data class for [[common\models\startdatabase\UserFans]].
* Data model definde model behavior and status code.
* @see \common\models\startdatabase\UserFans
*/
class UserFans extends BaseModelUserFans
{
    /**
     * The cache tag
     */
    const CACHE_TAG='common_models_startdata_UserFans';

    const EVENT_CANCEL_FOLLOWING='eventCancelFollowing';
    const EVENT_FOLLOWING   ='eventFollowing';

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
            ['attention_id','compare','compareAttribute'=>'fans_id','operator'=>'!='],
            [['attention_id','fans_id'],'exist','targetClass'=>User::className(),'targetAttribute'=>'id'],
        ]);
    }

    /**
    * @inheritdoc
    */
    public function scenarios()
    {
        return [
            'default' => [
                'attention_id',
                'fans_id',
            ],
            'search' => [
                'attention_id',
                'fans_id',
            ],
            'view' => [
                'attention_id',
                'fans_id',
            ],
            'update' => [
                'attention_id',
                'fans_id',
            ],
            'create' => [
                'attention_id',
                'fans_id',
            ],
        ];
    }


    public function behaviors()
    {
        return [
            'updatafollowing'=>[
                'class'=>UpdataFans::className(),
            ],
        ]; // TODO: Change the autogenerated stub
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAttention(){
        return $this->hasOne(Attention::className(),['id'=>'attention_id']);
    }


    /**
     * get relation columns
     * @return array
     */
    public static function columnRetions(){
        return [
            'attention_id'=>'Attention',
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
        $this->trigger(self::EVENT_FOLLOWING,new Event());
        parent::afterSave($insert , $changedAttributes); // TODO: Change the autogenerated stub
    }

    public function afterDelete()
    {
        TagDependency::invalidate(\yii::$app->cache,self::CACHE_TAG);
        $this->trigger(self::EVENT_CANCEL_FOLLOWING,new Event());
        parent::afterDelete(); // TODO: Change the autogenerated stub
    }

    public static function isExists($master,$fans){
        if($master && $fans){
            return static::find()->where([
                'attention_id'=>$master,
                'fans_id'=>$fans,
            ])->exists();
        }else{
            return null;
        }
    }
}
