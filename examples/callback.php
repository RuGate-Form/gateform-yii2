<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use GateForm\Yii2\CallbackWidget;

?>
<div class="callback-block">
    <?= CallbackWidget::widget([
        'hash' => 'cf_0123456789abcdef0123456789abcdef',
        'title' => 'Заказать звонок',
    ]) ?>
</div>
