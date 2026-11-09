<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var array{sku:string,name:string,url:string} $product */

use GateForm\Yii2\ReviewsWidget;

?>
<section class="product-reviews">
    <h2>Отзывы покупателей</h2>
    <?= ReviewsWidget::widget([
        'hash' => 'rv_0123456789abcdef0123456789abcdef',
        'productKey' => $product['sku'],
        'productName' => $product['name'],
        'productUrl' => $product['url'],
        'source' => 'yii2',
    ]) ?>
</section>
