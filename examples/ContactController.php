<?php

declare(strict_types=1);

/**
 * Контроллер формы. Токен проверяется валидатором модели.
 *
 * Не добавляйте одновременно Filter и Validator на одно действие:
 * токен одноразовый, повторная проверка вернёт TOKEN_ALREADY_USED.
 */

namespace app\controllers;

use app\models\ContactForm;
use Yii;
use yii\web\Controller;

class ContactController extends Controller
{
    public function actionIndex()
    {
        $model = new ContactForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            // ... обработка заявки

            Yii::$app->session->setFlash('success', 'Спасибо! Заявка отправлена.');

            return $this->refresh();
        }

        return $this->render('contact', ['model' => $model]);
    }
}
