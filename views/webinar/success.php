<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $data array */

$this->title = 'Webinar Created Successfully';
?>

<h1><?= Html::encode($this->title) ?></h1>

<p>Your webinar has been successfully created!</p>

<h2>Webinar Details</h2>
<pre><?= Html::encode(print_r($data, true)) ?></pre>
