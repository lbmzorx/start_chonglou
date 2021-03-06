<?php
namespace common\models\startdata;

use Yii;
use common\models\startdatabase\UrlCheck as BaseModelUrlCheck;
use yii\caching\TagDependency;

/**
* This is the data class for [[common\models\startdatabase\UrlCheck]].
* Data model definde model behavior and status code.
* @see \common\models\startdatabase\UrlCheck
*/
class UrlCheck extends BaseModelUrlCheck
{
    /**
     * The cache tag
     */
    const CACHE_TAG='common_models_startdata_UrlCheck';


    const STATUS_WAITING=0;
    const STATUS_CLICKED=1;
    const STATUS_USELESS=2;
    /**
    * 状态
    * 状态.tran:0=等待,1=已点击,2=失效.code:0=Waiting,1=Clicked,2=Useless.
    * @var array $status_code
    */
    public static $status_code = [0=>'Waiting',1=>'Clicked',2=>'Useless',];

    /**
     * get status code attribute list
     */
    public static function statusCodes(){
        return [
            'status'
        ];
    }

    /**
    * @inheritdoc
    */
    public function rules()
    {
        return array_merge(parent::rules(),[
            [['status'], 'in', 'range' => [0,1,2,]],
            [['num','status','expire_time'], 'default', 'value' =>0,],
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
                'md5',
                'url',
                'user_id',
                'ip',
                'num',
                'status',
                'auth_key',
                'expire_time',
                'add_time',
            ],
            'search' => [
                'id',
                'md5',
                'url',
                'user_id',
                'ip',
                'num',
                'status',
                'auth_key',
                'expire_time',
                'add_time',
            ],
            'view' => [
                'id',
                'md5',
                'url',
                'user_id',
                'ip',
                'num',
                'status',
                'auth_key',
                'expire_time',
                'add_time',
            ],
            'update' => [
                'md5',
                'url',
                'user_id',
                'ip',
                'num',
                'status',
                'auth_key',
                'expire_time',
            ],
            'create' => [
                'md5',
                'url',
                'user_id',
                'ip',
                'num',
                'status',
                'auth_key',
                'expire_time',
            ],
        ];
    }

    public function behaviors()
    {
        return [
            'timeUpdate'=>[
                'class' => \yii\behaviors\TimestampBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['add_time'],
                ],
            ],
            'getStatusCode'=>[
                'class' => \lbmzorx\components\behavior\StatusCode::className(),
                'category' =>'statuscode',
            ],
            'withOneUser'=>[
                'class' => \lbmzorx\components\behavior\WithOneUser::className(),
                'userClass'=> User::ClassName(),
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser(){
        return $this->hasOne(User::className(),['id'=>'user_id']);
    }

    /**
     * get relation columns
     * @return array
     */
    public static function columnRetions(){
        return [
            'user_id'=>'User',
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
