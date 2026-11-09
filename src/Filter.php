<?php

declare(strict_types=1);

namespace GateForm\Yii2;

use Yii;
use yii\base\ActionFilter;
use yii\web\HttpException;

/**
 * Фильтр действий контроллера: проверяет токен до выполнения действия.
 *
 * public function behaviors(): array
 * {
 *     return [
 *         'gateform' => [
 *             'class' => \GateForm\Yii2\Filter::class,
 *             'only' => ['contact', 'signup'],
 *         ],
 *     ];
 * }
 */
class Filter extends ActionFilter
{
    use ComponentAwareTrait;

    public string $message = 'Не удалось подтвердить, что вы человек. Обновите страницу и попробуйте снова.';

    /**
     * false — fail-open при недоступности сервиса считается ошибкой.
     */
    public bool $allowFailOpen = true;

    /**
     * @param \yii\base\ActionEvent $event
     */
    protected function beforeFilter($event)
    {
        $request = Yii::$app->getRequest();

        // Проверяем только POST: GET-страницы с формой должны открываться.
        if (!$request->getIsPost()) {
            return true;
        }

        $result = $this->resolveGateForm()->validateRequest($request);

        if (!$result->isValid() || ($result->isFailOpen() && !$this->allowFailOpen)) {
            throw new HttpException(400, $this->message);
        }

        return true;
    }
}
