<?php

declare(strict_types=1);

namespace GateForm\Yii2;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget as BaseWidget;
use yii\helpers\Html;

/**
 * Виджет связи GateForm.
 *
 * <?= \GateForm\Yii2\ContactWidget::widget(['id' => 'w_xxxxxxxx']) ?>
 */
final class ContactWidget extends BaseWidget
{
    public string $id = '';
    public string $baseUrl = '';

    public function init(): void
    {
        parent::init();

        $this->id = trim($this->id);
        if (!preg_match('/^[A-Za-z0-9_-]{3,64}$/', $this->id)) {
            throw new InvalidConfigException('GateForm: укажите id виджета связи вида w_...');
        }
    }

    public function run(): string
    {
        $configured = $this->baseUrl;
        if ($configured === '' && Yii::$app !== null) {
            $configured = (string) (Yii::$app->params['gateformUrl'] ?? '');
        }
        $configured = rtrim($configured !== '' ? $configured : 'https://gateform.ru', '/');
        if (!preg_match('#^https?://#i', $configured)) {
            throw new InvalidConfigException('GateForm: baseUrl должен начинаться с http:// или https://.');
        }

        return Html::tag('script', '', [
            'src' => $configured . '/widget.js',
            'data-widget-id' => $this->id,
            'defer' => true,
        ]);
    }
}
