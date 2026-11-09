# GateForm для Yii2

[GateForm](https://gateform.ru) — капча и защита форм от спама и ботов.
Пакет объединяет серверную проверку токенов и публичные виджеты Yii2.

- Сайт: <https://gateform.ru>
- Документация: <https://gateform.ru/docs>
- Поддержка: <info@gateform.ru>

## Состав

| Класс | Назначение |
| --- | --- |
| `GateForm\Yii2\Component` | Настройки и клиент SDK, доступен как `Yii::$app->gateform` |
| `GateForm\Yii2\Validator` | Проверка атрибута модели (`gf_token`) |
| `GateForm\Yii2\Filter` | Проверка токена до выполнения действия контроллера |
| `GateForm\Yii2\Widget` | Подключение `client.js` на страницу |
| `GateForm\Yii2\CallbackWidget` | Форма GateForm на сайте |
| `GateForm\Yii2\ReviewsWidget` | Отзывы товара |
| `GateForm\Yii2\ContactWidget` | Виджет связи |

## Требования

- PHP **8.1+**
- Yii2 **2.0.49+**
- Пакет `gateform/sdk`

## Установка

```bash
composer require gateform/yii2:^1.0
```

Пакет `gateform/sdk` установится автоматически.

## Настройка

`config/web.php`:

```php
'components' => [
    'gateform' => [
        'class' => \GateForm\Yii2\Component::class,
        'siteKey' => getenv('GATEFORM_SITE_KEY'),
        'secretKey' => getenv('GATEFORM_SECRET_KEY'),
        'baseUrl' => getenv('GATEFORM_BASE_URL') ?: 'https://gateform.ru',
        'failOpen' => false,
    ],
],
```

| Свойство | По умолчанию | Описание |
| --- | --- | --- |
| `siteKey` | — | Публичный ключ площадки |
| `secretKey` | — | Секретный ключ (только сервер) |
| `baseUrl` | `https://gateform.ru` | Адрес экземпляра GateForm |
| `timeout` | `5.0` | Таймаут запроса, секунды |
| `connectTimeout` | `3.0` | Таймаут соединения, секунды |
| `failOpen` | `false` | Принимать форму при недоступности сервиса |
| `tokenField` | `gf_token` | Имя поля с токеном |

## Капча: валидатор модели

Модель должна принимать токен из формы:

```php
namespace app\models;

use GateForm\Yii2\Validator as GateFormValidator;
use yii\base\Model;

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
            ['email', 'email'],
            [['gf_token'], GateFormValidator::class],
        ];
    }
}
```

Валидатор делает `gf_token` безопасным для `load()` и проверяет токен на сервере.
Пустой токен — ошибка валидации.

Дополнительные свойства валидатора:

```php
[['gf_token'], GateFormValidator::class, 'allowFailOpen' => false],
```

- `allowFailOpen = true` (по умолчанию) — если сервис недоступен и включён `failOpen`,
  форма принимается;
- `allowFailOpen = false` — fail-open тоже считается ошибкой.

## Капча: фильтр действия

Удобно, когда нет модели (например, действие обрабатывает `$_POST` напрямую):

```php
public function behaviors(): array
{
    return [
        'gateform' => [
            'class' => \GateForm\Yii2\Filter::class,
            'only' => ['contact'],
        ],
    ];
}
```

Фильтр проверяет только POST-запросы; GET-страницы открываются свободно.
При отказе выбрасывается `yii\web\HttpException` с кодом 400.

> **Важно:** не используйте `Filter` и `Validator` одновременно на одном действии.
> Токен одноразовый: вторая проверка вернёт `TOKEN_ALREADY_USED`.

## Виджет client.js

В представлении формы:

```php
use GateForm\Yii2\Widget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$form = ActiveForm::begin([
    'id' => 'contact-form',
    'options' => ['data-gateform' => true],
]);

echo $form->field($model, 'name')->textInput();
echo $form->field($model, 'email')->input('email');
echo $form->field($model, 'message')->textarea(['rows' => 5]);
echo Html::submitButton('Отправить', ['class' => 'btn btn-primary']);

ActiveForm::end();

echo Widget::widget(['formSelector' => '#contact-form']);
```

Параметры виджета:

| Параметр | По умолчанию | Описание |
| --- | --- | --- |
| `formSelector` | `form[data-gateform]` | CSS-селектор форм (можно несколько через запятую) |
| `tokenName` | `gf_token` | Имя скрытого поля с токеном |
| `clientJsUrl` | `{baseUrl}/client.js` | Свой адрес скрипта |
| `timeout` | — | Таймаут проверки в браузере, мс |
| `blockMessage` | — | Сообщение при отказе |

Виджет выводит только тег `<script>`; секретный ключ в HTML не попадает.

## Виджеты формы, отзывов и связи

Виджетам нужны только публичные ключи `cf_...`, `rv_...` и `w_...`.
Секретный ключ не требуется.

### Форма на сайте

```php
<?= \GateForm\Yii2\CallbackWidget::widget([
    'hash' => 'cf_0123456789abcdef0123456789abcdef',
    'title' => 'Заказать звонок',
]) ?>
```

### Отзывы товара

```php
<?= \GateForm\Yii2\ReviewsWidget::widget([
    'hash' => 'rv_0123456789abcdef0123456789abcdef',
    'productKey' => $product->sku,
    'productName' => $product->name,
    'productUrl' => $product->url,
]) ?>
```

### Виджет связи

```php
<?= \GateForm\Yii2\ContactWidget::widget(['id' => 'w_xxxxxxxx']) ?>
```

Для виджетов можно задать общий адрес в `params`:

```php
'params' => [
    'gateformUrl' => 'https://gateform.ru',
],
```

## Fail-open

По умолчанию интеграция работает **fail-closed**: при недоступности сервиса
проверки форма отклоняется. Чтобы не терять заявки при сбоях, включите:

```php
'failOpen' => true,
```

Тогда при сетевой ошибке, таймауте, HTTP 5xx или 429 форма будет принята.
Логируйте такие случаи, например через `Yii::warning()` в своём обработчике.

## Лицензия

MIT. См. [LICENSE](LICENSE).

Разработчик: **GateForm** — <https://gateform.ru>
