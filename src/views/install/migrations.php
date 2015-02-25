<?php
/**
 * @var \yii\web\View $this
 * @var integer $runMigrationCount
 * @var integer $totalMigrationCount
 */

use yii\bootstrap\Progress;
use yii\helpers\Html;

?>

<div class="page-header">
    <h1><?= Yii::t('core', 'Applying Migrations') ?></h1>
</div>

<?= Progress::widget(
    [
        'percent' => ($totalMigrationCount === 0)?(100):($runMigrationCount / $totalMigrationCount * 100),
        'options' => ['class' => 'active progress-striped']
    ]
) ?>
