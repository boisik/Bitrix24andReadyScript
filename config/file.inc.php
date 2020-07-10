<?php

namespace CrmB24\Config;

use RS\Orm\ConfigObject;
use RS\Orm\Type;

/**
 * Класс конфигурации модуля
 */
class File extends ConfigObject
{
    public function _init()
    {
        parent::_init()->append(array(
            'crm_hook' => new Type\Varchar(array(
                'description' => t('Адрес Хука Bitrix24'),
                'hint' => t('Создается в ЛК CRMки'),
                'default' => 'https://mavistep.bitrix24.ua/rest/28/4fpq17o0uuw8b72/'
            )),

            'debug_mode' => new Type\Integer(array(
                'description' => t('Режим отладки'),
                'checkboxView' => array(1, 0)
            )),
        ));
    }
}
