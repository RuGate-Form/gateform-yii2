<?php

declare(strict_types=1);

namespace GateForm\Yii2;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget as BaseWidget;
use yii\helpers\Html;

/**
 * Форма на сайте GateForm.
 *
 * <?= \GateForm\Yii2\CallbackWidget::widget([
 *     'hash' => 'cf_0123456789abcdef0123456789abcdef',
 *     'title' => 'Заказать звонок',
 * ]) ?>
 */
final class CallbackWidget extends BaseWidget
{
    public string $hash = '';
    public string $title = '';
    public string $target = '';
    public string $baseUrl = '';

    public function init(): void
    {
        parent::init();

        $this->hash = strtolower(trim($this->hash));
        if (!preg_match('/^cf_[a-f0-9]{32}$/', $this->hash)) {
            throw new InvalidConfigException('GateForm: укажите публичный ключ callback-формы вида cf_...');
        }

        $this->baseUrl = $this->resolveBaseUrl();
        if ($this->target !== '' && !preg_match('/^#[A-Za-z][A-Za-z0-9_-]{0,63}$/', $this->target)) {
            throw new InvalidConfigException('GateForm: target должен быть CSS-идентификатором вида #gateform-callback.');
        }
    }

    public function run(): string
    {
        $attributes = [
            'src' => $this->baseUrl . '/callback-embed.js',
            'data-callback-form' => $this->hash,
            'defer' => true,
        ];
        if ($this->target !== '') {
            $attributes['data-target'] = $this->target;
        }

        $html = Html::tag('script', '', $attributes);
        if ($this->title !== '') {
            $html = Html::tag('h3', Html::encode($this->title), ['class' => 'gateform-callback-title']) . $html;
        }

        return Html::tag('div', $html, ['class' => 'gateform-callback']);
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
