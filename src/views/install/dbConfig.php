<?php
/**
 * @var \yii\web\View $this
 * @var \exchangecore\filemanager\models\install\DbConfig $model
 */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

?>

<div class="page-header">
    <h1>Database Configuration</h1>
</div>

<?php $form = ActiveForm::begin(['id' => 'database-config']); ?>

<?= $form->field($model, 'type')->dropDownList($model->types) ?>
<?= $form->field($model, 'host') ?>
<?= $form->field($model, 'database') ?>
<?= $form->field($model, 'username') ?>
<?= $form->field($model, 'password')->passwordInput() ?>

<div class="form-group">
    <?= Html::submitButton(Yii::t('core', 'Save Database Configuration'), ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end() ?>