<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Webinar */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Update Webinar: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Webinars', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'event_id' => $model->event_id]];
$this->params['breadcrumbs'][] = 'Update';
?>

<div class="webinar-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'event_id')->hiddenInput()->label(false); ?>

    <div class="form-group">
        <?= Html::submitButton('Update Webinar', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
