<?php

declare(strict_types=1);

namespace GateForm\Yii2;

use yii\base\Widget as BaseWidget;
use yii\helpers\Html;

/**
 * Подключает client.js на страницу.
 *
 * В представлении:
 *
 *     <?= \GateForm\Yii2\Widget::widget() ?>
 *     <?= \GateForm\Yii2\Widget::widget(['formSelector' => '#contact-form']) ?>
 */
class Widget extends BaseWidget
{
    use ComponentAwareTrait;

    /**
     * CSS-селектор форм. Можно указать несколько через запятую.
     */
    public string $formSelector = 'form[data-gateform]';

    public string $tokenName = \GateForm\Config::DEFAULT_TOKEN_FIELD;

    /**
     * По умолчанию — {baseUrl}/client.js.
     */
    public ?string $clientJsUrl = null;

    /**
     * Таймаут ожидания проверки в браузере, мс (1000..30000).
     */
    public ?int $timeout = null;

    /**
     * Сообщение при отказе. По умолчанию — текст client.js.
     */
    public ?string $blockMessage = null;

    public function run(): string
    {
        $component = $this->resolveGateForm();

        $attributes = [
            'src' => $this->clientJsUrl ?? $component->clientJsUrl(),
            'data-site-key' => $component->siteKey,
            'data-form' => $this->formSelector,
            'data-token-name' => $this->tokenName,
            'defer' => 'defer',
        ];

        if ($this->timeout !== null) {
            $attributes['data-timeout'] = (string) $this->timeout;
        }

        if ($this->blockMessage !== null && $this->blockMessage !== '') {
            $attributes['data-block-message'] = $this->blockMessage;
        }

        return Html::tag('script', '', $attributes);
    }
}
