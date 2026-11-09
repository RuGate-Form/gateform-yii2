<?php

declare(strict_types=1);

namespace GateForm\Yii2;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget as BaseWidget;
use yii\helpers\Html;

/**
 * Отзывы товара GateForm.
 *
 * <?= \GateForm\Yii2\ReviewsWidget::widget([
 *     'hash' => 'rv_0123456789abcdef0123456789abcdef',
 *     'productKey' => $product->sku,
 *     'productName' => $product->name,
 *     'productUrl' => $product->url,
 * ]) ?>
 */
final class ReviewsWidget extends BaseWidget
{
    public string $hash = '';
    public string $productKey = '';
    public string $productName = '';
    public string $productUrl = '';
    public string $source = 'yii2';
    public string $target = '';
    public string $baseUrl = '';

    public function init(): void
    {
        parent::init();

        $this->hash = strtolower(trim($this->hash));
        if (!preg_match('/^rv_[a-f0-9]{32}$/', $this->hash)) {
            throw new InvalidConfigException('GateForm: укажите публичный ключ отзывов вида rv_...');
        }

        if ($this->target !== '' && !preg_match('/^#[A-Za-z][A-Za-z0-9_-]{0,63}$/', $this->target)) {
            throw new InvalidConfigException('GateForm: target должен быть CSS-идентификатором вида #gateform-reviews.');
        }
    }

    public function run(): string
    {
        $attributes = [
            'src' => $this->resolveBaseUrl() . '/review-embed.js',
            'data-gateform-reviews' => $this->hash,
            'data-source' => $this->source !== '' ? $this->source : 'yii2',
            'defer' => true,
        ];

        foreach ([
            'data-product-key' => $this->productKey,
            'data-product-name' => $this->productName,
            'data-product-url' => $this->productUrl,
            'data-target' => $this->target,
        ] as $name => $value) {
            if ($value !== '') {
                $attributes[$name] = $value;
            }
        }

        return Html::tag('div', Html::tag('script', '', $attributes), ['class' => 'gateform-reviews']);
    }

    private function resolveBaseUrl(): string
    {
        $configured = $this->baseUrl;
        if ($configured === '' && Yii::$app !== null) {
            $configured = (string) (Yii::$app->params['gateformUrl'] ?? '');
        }
        $configured = rtrim($configured !== '' ? $configured : 'https://gateform.ru', '/');

        if (!preg_match('#^https?://#i', $configured)) {
            throw new InvalidConfigException('GateForm: baseUrl должен начинаться с http:// или https://.');
        }

        return $configured;
    }
}
