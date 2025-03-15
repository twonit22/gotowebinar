<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Webinar */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Webinars', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="webinar-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <strong>Event ID:</strong> <?= Html::encode($model->event_id) ?>
    </p>
    <p>
        <strong>Webinar Key:</strong> <?= Html::encode($model->webinar_key) ?>
    </p>
    <p>
        <strong>Name:</strong> <?= Html::encode($model->name) ?>
    </p>
    <p>
        <strong>Description:</strong> <?= Html::encode($model->description) ?>
    </p>
    <p>
      <strong>Start Time:</strong> <?= Yii::$app->formatter->asDatetime($model->start_time, 'php:Y-m-d H:i:s') ?>
    </p>
    <p>
      <strong>End Time:</strong> <?= Yii::$app->formatter->asDatetime($model->end_time, 'php:Y-m-d H:i:s') ?>
    </p>

    <p>
        <?= Html::a('Update', Url::to(['update', 'event_id' => $model->event_id]), ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'event_id' => $model->event_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
        <?php if (empty($model->webinar_key)): ?>
          <?= Html::a('Create Webinar in GoToWebinar', ['webinar/create-goto-webinar', 'event_id' => $model->event_id], [
            'class' => 'btn btn-success',
            'data' => [
                'confirm' => 'Are you sure you want to create this webinar in GoToWebinar?',
                'method' => 'post',
            ],
          ]) ?>
        <?php endif; ?>
    </p>

</div>
