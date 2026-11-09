<?php

declare(strict_types=1);

namespace GateForm\Yii2;

use yii\validators\Validator as BaseValidator;

/**
 * Валидатор атрибута модели Yii2.
 *
 * Модель должна принимать токен из формы:
 *
 *     public $gf_token;
 *
 *     public function rules(): array
 *     {
 *         return [
 *             [['gf_token'], \GateForm\Yii2\Validator::class],
 *         ];
 *     }
 */
class Validator extends BaseValidator
{
    use ComponentAwareTrait;

    public string $message = 'Не удалось подтвердить, что вы человек. Обновите страницу и попробуйте снова.';

    /**
     * false — fail-open при недоступности сервиса считается ошибкой.
     */
    public bool $allowFailOpen = true;

    /**
     * Пустой токен — это ошибка, а не «пропустить проверку».
     *
     * @var bool
     */
    public $skipOnEmpty = false;

    public function validateAttribute($model, $attribute): void
    {
        $value = $model->{$attribute};

        if (!is_string($value) || trim($value) === '') {
            $this->addError($model, $attribute, $this->message);
            return;
        }

        $result = $this->resolveGateForm()->validate($value);

        if (!$result->isValid()) {
            $this->addError($model, $attribute, $this->message);
            return;
        }

        if ($result->isFailOpen() && !$this->allowFailOpen) {
            $this->addError($model, $attribute, $this->message);
        }
    }
}
