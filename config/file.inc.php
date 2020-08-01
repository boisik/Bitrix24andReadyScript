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
            'id_deal_manager' => new Type\Varchar(array(
                'description' => t('идентификтор менеджера обработки сделок в CRM'),
                'hint' => t('нужно посмотреть в CRM идентификатор сотрудника, и вписать.'),

            )),
            'id_lead_manager' => new Type\Varchar(array(
                'description' => t('идентификтор менеджера обработки лидов в CRM'),
                'hint' => t('нужно посмотреть в CRM идентификатор сотрудника, и вписать.'),

            )),
            'enable_lead' => new Type\Integer(array(
                'description' => t('Импортировать Лиды'),
                'checkboxView' => array(1, 0)
            )),
            'enable_deal' => new Type\Integer(array(
                'description' => t('Импортировать Сделки'),
                'checkboxView' => array(1, 0)
            )),

            'enable_log' => new Type\Integer(array(
                'description' => t('вести логирование'),
                'checkboxView' => array(1, 0)
            )),
            'enable_products_import' => new Type\Integer(array(
                'description' => t('при следующих запусках крона импортировать товары'),
                'checkboxView' => array(1, 0)
            )),
            'enable_products_update' => new Type\Integer(array(
                'description' => t('при следующих запусках крона обновить товары'),
                'checkboxView' => array(1, 0)
            )),
            'enable_products_delete' => new Type\Integer(array(
                'description' => t('при следующих запусках крона удалить товары'),
                'checkboxView' => array(1, 0)
            )),
        ));
    }


    /**
     * Возвращает значения свойств по-умолчанию
     *
     * @return array
     */
    public static function getDefaultValues()
    {
        return parent::getDefaultValues() + array(
                'tools' => array(

                    array(
                        'url' => \RS\Router\Manager::obj()->getAdminUrl('deleteLog', array(), 'crmb24-tools'),
                        'title' => t('Очистить лог запросов'),
                        'description' => t('Удаляет лог файл'),
                    ),
                    array(
                        'url' => \RS\Router\Manager::obj()->getAdminUrl('showLog', array(), 'crmb24-tools'),
                        'title' => t('Просмотреть лог запросов'),
                        'description' => t('Открывает в новом окне журнал обмена данными с Бытрикс24 CRM '),
                        'target' => '_blank',
                        'class' => ' ',
                    )
                )
            );
    }
}
