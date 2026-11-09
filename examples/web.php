<?php

declare(strict_types=1);

/**
 * Фрагмент конфигурации приложения Yii2.
 * Добавьте компонент в config/web.php (или config/main.php).
 */

return [
    'components' => [
        'gateform' => [
            'class' => \GateForm\Yii2\Component::class,
            'siteKey' => getenv('GATEFORM_SITE_KEY'),
            'secretKey' => getenv('GATEFORM_SECRET_KEY'),
            'baseUrl' => getenv('GATEFORM_BASE_URL') ?: 'https://gateform.ru',
            // true — принимать форму при недоступности сервиса проверки.
            'failOpen' => false,
            // 'timeout' => 5.0,
            // 'connectTimeout' => 3.0,
            // 'tokenField' => 'gf_token',
        ],
    ],
];
