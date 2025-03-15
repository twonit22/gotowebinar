<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>

<h1>Webinars <?= Html::a('Create New Webinar', ['webinar/create'], ['class' => 'btn btn-success']) ?></h1>

<div class="webinar-list">
    <!-- Start Table -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($models as $model): ?>
                <tr>
                    <td><?= Html::encode($model->event_id) ?></td>
                    <td><?= Html::encode($model->name) ?></td>
                    <td><?= Html::encode($model->description) ?></td>
                    <td>
                        <?= Html::a('View', Url::to(['view', 'event_id' => $model->event_id]), ['class' => 'btn btn-info btn-sm']) ?>
                        <?= Html::a('Update', Url::to(['update', 'event_id' => $model->event_id]), ['class' => 'btn btn-primary btn-sm']) ?>
                        <?= Html::a('Delete', Url::to(['delete', 'event_id' => $model->event_id]), [
                            'class' => 'btn btn-danger btn-sm',
                            'data' => [
                                'confirm' => 'Are you sure you want to delete this webinar?',
                                'method' => 'post',
                            ],
                        ]) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
