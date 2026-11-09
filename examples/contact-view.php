<?php

declare(strict_types=1);

/**
 * Представление формы.
 *
 * options['data-gateform'] — маркер для client.js.
 * Виджет подключает client.js и передаёт ему site_key и селектор формы.
 */

use GateForm\Yii2\Widget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var app\models\ContactForm $model */

$form = ActiveForm::begin([
    'id' => 'contact-form',
    'options' => ['data-gateform' => true],
]);

echo $form->field($model, 'name')->textInput();
echo $form->field($model, 'email')->input('email');
echo $form->field($model, 'message')->textarea(['rows' => 5]);

echo Html::submitButton('Отправить', ['class' => 'btn btn-primary']);

ActiveForm::end();

echo Widget::widget([
    'formSelector' => '#contact-form',
]);
