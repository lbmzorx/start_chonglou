<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\startdata\UrlCheck */

$this->title = Yii::t('app', 'Create {modelname}', [
    'modelname' => Yii::t('app', 'Url Checks'),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Url Checks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="url-check-create">
    <?= \yii\widgets\Breadcrumbs::widget([
    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
    ]) ?>    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
