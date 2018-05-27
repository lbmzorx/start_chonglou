<?php
namespace frontend\widget;

use common\models\startdata\ArticleCommit;
use lbmzorx\components\helper\TreeHelper;
use Yii;
use common\models\startdata\ArticleCate;
use yii\caching\TagDependency;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Created by Administrator.
 * Date: 2018/5/20 14:38
 * github: https://github.com/lbmzorx
 */
class ArticleCommitWidget extends PanelWidget
{
    public $condition=[];
    public $ulOption=[];
    public $liOption=[];
    public $articleCommit;
    public $article_id;
    public $commitCount;

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        if(empty($this->ulOption['class'])){
            $this->ulOption['class']='list-group';
        }
        if(empty($this->liOption['class'])){
            $this->liOption['class']='list-group-item';
        }
    }

    public function getHeadContent()
    {
        return Html::tag('h4',Yii::t('app','Commits').'   '.Html::tag('span',$this->commitCount,[]).\yii::t('app','Rows'));//Html::tag('h3',Yii::t('app','Category'),['class'=>'panel-title']); // TODO: Change the autogenerated stub
    }

    public function getBodyContent()
    {
        $data=$this->getArticleCateData($this->condition);
        $li=$this->treeCommit($data,[]);
        $str='';
        foreach ($li as $v){
            $str.=$v;
        }
        return Html::tag('div',$str,$this->ulOption); // TODO: Change the autogenerated stub
    }

    public function treeCommit($input,$options){
        $data = [];
        foreach ($input as $k => $v){
            if(isset($v['sub'])){
                $data[$v['id']]= $v['name'];
                $userInfo=Html::a(Html::img(Html::encode($v['user']['head_img']),['alt'=>Html::encode($v['user']['username'])]),['user/index','id'=>$v['user']['id']]);
                $left=Html::tag('div',$userInfo,['class'=>'media-left']);

                $userName=Html::a($v['user']['username']).'    '.date('Y-m-d',$v['add_time']);
                $content=Html::tag('div',$v['content'],['class'=>'commit-box']);
                $footer=Html::tag('div',Html::a('<i class="fas fa-reply"></i>'.\yii::t('app','Reply') ,'javascript:void(0)',[]),['class'=>'media-footer']);

                $sub=$this->treeCommit($v['sub'],$options);

                $body=Html::tag('div',$userName.$content.$footer.$sub,['class'=>'media-body']);
                $data[$v['id']]=$left.$body;
            }else{

                $userInfo=Html::a(Html::img(Html::encode($v['user']['head_img']),['alt'=>Html::encode($v['user']['username'])]),['user/index','id'=>$v['user']['id']]);
                $left=Html::tag('div',$userInfo,['class'=>'media-left']);

                $userName=Html::a($v['user']['username']).'    '.date('Y-m-d',$v['add_time']);
                $content=Html::tag('div',$v['content'],['class'=>'commit-box']);
                $footer=Html::tag('div',Html::a('<i class="fas fa-reply"></i>'.\yii::t('app','Reply') ,'javascript:void(0)',[]),['class'=>'media-footer']);

                $body=Html::tag('div',$userName.$content.$footer,['class'=>'media-body']);
                $data[$v['id']]=$left.$body;
            }
        }
        return $data;
    }

    public function getArticleCateData($condition){
        $cache=\yii::$app->cache;
        $key=serialize(['type'=>'articleCate',__METHOD__,'article_id'=>$this->article_id]);
        if($data=$cache->get($key)){
            return $data;
        }else{
            $data=ArticleCommit::find()->where([
                    'article_id'=>$this->article_id,
                    'status'=>ArticleCommit::STATUS_AUDIT_PASS,
                ]
            )->andFilterWhere($condition)->orderBy(['id'=>SORT_ASC])->with('user')->asArray()->all();

            if($data){
                $data=TreeHelper::array_cate_as_subarray($data);
            }

            $cache->set($key,$data,(3600*24*30+rand(1,600)),new TagDependency([
                'tags'=>ArticleCommit::CACHE_TAG,
            ]));
            return $data;
        }
    }
}