<?php
/**
 * @var \yii\web\View $this
 * @var string $content
 */

use exchangecore\filemanager\assets\InstallAsset;
use yii\helpers\Html;

InstallAsset::register($this);

$this->beginPage();
?>

<!doctype html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?> | <?= Yii::t('app', 'File Manager Install') ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<nav class="navbar navbar-default">
    <div class="container">
        <div class="navbar-header">
            <a class="navbar-brand" href="#"><?= Yii::t('app', 'File Manager Install') ?></a>
        </div>
    </div>
</nav>

<div class="container">
    <?= $content ?>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>