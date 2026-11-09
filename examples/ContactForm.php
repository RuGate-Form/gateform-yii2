<?php

declare(strict_types=1);

namespace app\models;

use GateForm\Yii2\Validator as GateFormValidator;
use yii\base\Model;

/**
 * Модель формы обратной связи.
 *
 * Свойство $gf_token принимает токен, который client.js добавляет в форму.
 * Валидатор GateForm делает атрибут безопасным для массового заполнения
 * и проверяет токен на сервере.
 */
class ContactForm extends Model
{
    public $name;
    public $email;
    public $message;
    public $gf_token;

    public function rules(): array
    {
        return [
            [['name', 'email', 'message'], 'required'],
            ['name', 'string', 'max' => 100],
            ['email', 'email'],
            ['email', 'string', 'max' => 254],
            ['message', 'string', 'max' => 5000],
            [['gf_token'], GateFormValidator::class],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Имя',
            'email' => 'Email',
            'message' => 'Сообщение',
        ];
    }
}
