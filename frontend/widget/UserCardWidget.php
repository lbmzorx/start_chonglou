<?php
namespace frontend\widget;

use common\models\startdata\User;
use common\models\startdata\UserFans;
use common\models\startdata\UserInfo;
use Yii;
use common\models\startdata\ArticleCate;
use yii\base\Widget;
use yii\caching\TagDependency;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * Created by Administrator.
 * Date: 2018/5/20 14:38
 * github: https://github.com/lbmzorx
 */
class UserCardWidget extends Widget
{
    public $condition=[];
    public $userId;
    public $options=[];

    public function run()
    {
        if($this->userId){
            echo $this->getBodyContent();
        }else{
            echo '';
        }

    }

    /**
     * @return string
     */
    public function getBodyContent()
    {
        $data=$this->getUserInfo($this->condition);

        $head_img= Html::tag('div',Html::a( Html::tag('div',Html::img( $data['head_img']?:'/img/default-user-50.png',['alt'=>$data['username'],'onerror'=>"this.src='/img/default-user-50.png'"])),['/user','id'=>$this->userId]),['class'=>'media-left mr-2']);


        $username=Html::tag('div',Html::a(Html::tag('h3',$data['username'],['class'=>'user-name-info']),['/user','id'=>$this->userId]),['class'=>'media-body']);

        $userinfo=Html::tag('span',\yii::t('app','Followers').
            Html::tag('em',isset($data['userInfo']['fans'])?$data['userInfo']['fans']:'0'),
            ['class'=>'userInfo']).
        Html::tag('span',\yii::t('app','Followings').
            Html::tag('em',isset($data['userInfo']['attention'])?$data['userInfo']['attention']:0),
            ['class'=>'userInfo']).
        Html::tag('span',\yii::t('app','Articles').
            Html::tag('em',isset($data['userInfo']['article'])?$data['userInfo']['article']:0),
            ['class'=>'userInfo']);

        $status=Html::tag('div',$userinfo,['class'=>'user-status']);

        $footerContent='';
        if( $this->userId!=\yii::$app->user->id){
            $attention=UserFans::isExists($this->userId,\yii::$app->user->id);
            $footerContent=Html::tag('div',
                    Html::a(Html::tag('i','',['class'=>'fa fa-'.($attention?'eye-slash':'eye')]).'&nbsp'.\yii::t('app','Follow'),'javascript:void(0)',[
                        'class'=>'btn btn-sm btn-primary',
                        'id' =>'follow-this-user',
                        'data-user'=>$data['username'],
                        'title'=>($attention?\yii::t('app','Following Cancel'):\yii::t('app','Following')),
                        'data-following'=>$attention,
                ]),['class'=>'btn-group']).
                Html::tag('div',Html::a(Html::tag('i','',['class'=>'fa fa-plus']).'&nbsp'.\yii::t('app','Chat'),'javascript:void(0)',
                    [
                        'class'=>'btn btn-sm btn-info',
                        'id' =>'chat-this-user',
                        'data-user'=>$data['username'],
                    ]),['class'=>'btn-group']);
            $this->renderJs($this->userId,$attention);
        }
        $footer=Html::tag('div',$footerContent,['class'=>'user-footer ']);
        $body=Html::tag('div',$status.$footer);



        $frame=Html::tag('div',Html::tag('div',$head_img.$username.$body,['class'=>'card-body']),['class'=>'card']);
        return $frame; // TODO: Change the autogenerated stub
    }


    public function getUserInfo($condition){
        if($this->userId){
            $cache=\yii::$app->cache;
            $key=serialize(['type'=>'user_info',__METHOD__,'userId'=>$this->userId]);
//            if($data=$cache->get($key)){
//                return $data;
//            }else{
                $data=User::find()->select('id,username,head_img')->where([
                        'status'=>User::STATUS_ACTIVE,
                        'id'=>$this->userId,
                    ]
                )->with('userInfo')->andFilterWhere($condition)->asArray()->one();
//                $cache->set($key,$data,(3600*24*30+rand(1,600)),new TagDependency([
//                    'tags'=>UserInfo::CACHE_TAG,
//                ]));
                return $data;
//            }
        }
    }

    public function renderJs($userid,$attention){
        $actionAttention=Url::to(['/user/attention']);
        $csrfParam=\yii::$app->request->csrfParam;
        $csrfTocken=\yii::$app->request->csrfToken;
        $chatUrl=Url::to(['/user/chat']);
        $errormsg=\yii::t('app','Request Error!');
        $loginMsg=\yii::t('app','Please Login!');
        $loginUrl=\yii\helpers\Url::to(['/site/login']);

        $isGuest=\yii::$app->user->isGuest;

        if($isGuest){
            $chat=<<<cat
        layer.open({
            type:2,
            area:[],
            content:'{$chatUrl}',
        });
cat;
        }else{
            $chat='';
        }

        $canelMsg=\yii::t('app','Do you want to canel this following?');
        $script=<<<SCRIPT
var layer;
layui.use(['layer'],function(){
    layer=layui.layer;
});
function following(){
    $.ajax({
        url:'{$actionAttention}',
        type:'post',                   
        data:{
            '{$csrfParam}':'{$csrfTocken}',
            'FansForm[following_id]':{$userid}
        },
        dataType: 'json',
        success:function(res){
            if(res.status){
                layer.msg(res.msg);
                location.reload();
            }else{
                layer.alert(res.msg);
            }
        },
        error:function(res){
            if(res.status==403){
                layer.alert('{$loginMsg}',function () {
                    location.href='{$loginUrl}';
                });
            }else{
                layer.alert('{$errormsg}');
            }                        
        }                    
    });
}
$('#follow-this-user').click(function(){
    var isfollowing=$(this).attr('data-following');
    if(isfollowing==true){
        layer.confirm('{$canelMsg}',function(){
            following();
        });
    }else{
        following();      
    }
});

$('#chat-this-user').click(function(){
    {$chat}
});

SCRIPT;

        \yii::$app->view->registerJs($script);
    }
}