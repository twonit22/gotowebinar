<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Webinar */

$this->title = 'Create Webinar';
$this->params['breadcrumbs'][] = ['label' => 'Webinars', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="webinar-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="webinar-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

        <!-- No need to include event_id in the form since it's auto-incremented -->

        <div class="form-group">
            <?= Html::submitButton('Create Webinar', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

        <!-- Display validation errors -->
        <?php if ($model->hasErrors()): ?>
            <div class="alert alert-danger">
                <strong>There were some issues with your submission:</strong>
                <ul>
                    <?php foreach ($model->errors as $error): ?>
                        <li><?= implode(', ', $error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

    </div>

</div>
