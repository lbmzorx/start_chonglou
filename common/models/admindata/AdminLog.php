<?php
namespace common\models\admindata;

use Yii;
use common\models\admindatabase\AdminLog as BaseModelAdminLog;
use yii\caching\TagDependency;

/**
* This is the data class for [[common\models\admindatabase\AdminLog]].
* Data model definde model behavior and status code.
* @see \common\models\admindatabase\AdminLog
*/
class AdminLog extends BaseModelAdminLog
{
    /**
     * The cache tag
     */
    const CACHE_TAG='common_models_admindata_AdminLog';


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
            [['route'], 'default', 'value' =>'',],
            [['edit_time'], 'default', 'value' =>'0',],
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
                'admin_id',
                'route',
                'description',
                'add_time',
                'edit_time',
            ],
            'search' => [
                'id',
                'admin_id',
                'route',
                'description',
                'add_time',
                'edit_time',
            ],
            'view' => [
                'id',
                'admin_id',
                'route',
                'description',
                'add_time',
                'edit_time',
            ],
            'update' => [
                'admin_id',
                'route',
                'description',
            ],
            'create' => [
                'admin_id',
                'route',
                'description',
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
                    self::EVENT_BEFORE_UPDATE => ['edit_time'],
                ],
            ],
        ];
    }

    public function afterFind()
    {
        $this->description = str_replace([
            '{{%ADMIN_USER%}}',
            '{{%BY%}}',
            '{{%CREATED%}}',
            '{{%UPDATED%}}',
            '{{%DELETED%}}',
            '{{%ID%}}',
            '{{%RECORD%}}'
        ], [
            yii::t('app', 'Admin user'),
            yii::t('app', 'through'),
            yii::t('app', 'created'),
            yii::t('app', 'updated'),
            yii::t('app', 'deleted'),
            yii::t('app', 'id'),
            yii::t('app', 'record')
        ], $this->description);
        $this->description = preg_replace_callback('/\d{10}/', function ($matches) {
            return date('Y-m-d H:i:s', $matches[0]);
        }, $this->description);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAdmin(){
        return $this->hasOne(Admin::className(),['id'=>'admin_id']);
    }

    /**
     * get relation columns
     * @return array
     */
    public static function columnRetions(){
        return [
            'admin_id'=>'Admin',
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
