<?php

declare(strict_types=1);

namespace GateForm\Yii2;

use yii\base\InvalidConfigException;
use yii\web\Request;

/**
 * Компонент приложения Yii2.
 *
 * Регистрация в config/web.php:
 *
 * 'components' => [
 *     'gateform' => [
 *         'class' => \GateForm\Yii2\Component::class,
 *         'siteKey' => getenv('GATEFORM_SITE_KEY'),
 *         'secretKey' => getenv('GATEFORM_SECRET_KEY'),
 *         'baseUrl' => getenv('GATEFORM_BASE_URL'),
 *     ],
 * ],
 */
class Component extends \yii\base\Component
{
    public string $siteKey = '';
    public string $secretKey = '';
    public string $baseUrl = \GateForm\Config::DEFAULT_BASE_URL;
    public float $timeout = \GateForm\Config::DEFAULT_TIMEOUT;
    public float $connectTimeout = \GateForm\Config::DEFAULT_CONNECT_TIMEOUT;

    /**
     * true — принимать форму, если сервис проверки недоступен.
     * false — отклонять (безопасный режим по умолчанию).
     */
    public bool $failOpen = false;

    public string $tokenField = \GateForm\Config::DEFAULT_TOKEN_FIELD;

    private ?\GateForm\Client $sdk = null;

    public function init(): void
    {
        parent::init();
        $this->sdk = $this->buildClient();
    }

    /**
     * SDK для нестандартных сценариев.
     */
    public function getSdk(): \GateForm\Client
    {
        if ($this->sdk === null) {
            $this->sdk = $this->buildClient();
        }

        return $this->sdk;
    }

    public function validate(string $token): \GateForm\ValidationResult
    {
        return $this->getSdk()->validate($token);
    }

    /**
     * Проверяет токен из текущего POST-запроса.
     */
    public function validateRequest(?Request $request = null): \GateForm\ValidationResult
    {
        $request = $request ?? \Yii::$app->getRequest();
        $value = $request->post($this->tokenField, '');

        return $this->validate(is_string($value) ? $value : '');
    }

    public function clientJsUrl(): string
    {
        return $this->getSdk()->config()->clientJsUrl();
    }

    private function buildClient(): \GateForm\Client
    {
        try {
            return new \GateForm\Client(new \GateForm\Config(
                $this->siteKey,
                $this->secretKey,
                $this->baseUrl,
                $this->timeout,
                $this->connectTimeout,
                $this->failOpen,
                $this->tokenField
            ));
        } catch (\GateForm\Exception\ConfigurationException $exception) {
            throw new InvalidConfigException(
                'GateForm: ' . $exception->getMessage(),
                0,
                $exception
            );
        }
    }
}
