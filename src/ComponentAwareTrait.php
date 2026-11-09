<?php

declare(strict_types=1);

namespace GateForm\Yii2;

use Yii;
use yii\base\InvalidConfigException;

/**
 * Разрешает компонент GateForm из его id или готового экземпляра.
 */
trait ComponentAwareTrait
{
    /**
     * @var Component|string id компонента приложения или готовый экземпляр
     */
    public $component = 'gateform';

    protected function resolveGateForm(): Component
    {
        $component = is_string($this->component)
            ? Yii::$app->get($this->component)
            : $this->component;

        if (!$component instanceof Component) {
            throw new InvalidConfigException(
                'Компонент GateForm не найден. Зарегистрируйте его в config/web.php.'
            );
        }

        return $component;
    }
}
