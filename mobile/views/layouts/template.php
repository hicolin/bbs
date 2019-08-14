<?php
/* @var $this \yii\web\View */
use yii\helpers\Url;
?>

// header
<?php $this->beginContent('@app/views/layouts/header.php') ?>
<?php $this->endContent() ?>

<?= $this->render('@app/views/layouts/header') ?>

// footer
<?php $this->beginContent('@app/views/layouts/footer.php') ?>
<?php $this->endContent() ?>

<?= $this->render('@app/views/layouts/footer') ?>

// css
<?php $this->beginBlock('header') ?>
<style>

</style>
<?php $this->endBlock() ?>

// js
<?php $this->beginBlock('footer') ?>
<script>

</script>
<?php $this->endBlock() ?>

// user
<?php if (Yii::$app->user->isGuest): ?>

<?php else: ?>

<?php endif; ?>
