<?php

namespace CrmB24\Config;

use RS\Event\HandlerAbstract;

use \RS\Orm\Type;
/**
 * Класс содержит обработчики событий, на которые подписан модуль
 */
class Handlers extends HandlerAbstract
{
    /**
     * Добавляет подписку на события
     *
     * @return void
     */
    function init()
    {
        $this ->bind('orm.init.catalog-product');

    }

    /**
     * Добавляет вкладку Файлы к товару
     */
    public static function ormInitCatalogProduct($product)
    {
        $product->getPropertyIterator()->append(array(
            "Bitrix24",
            'bitrix_id' => new Type\Integer(array(
                'visible' => true,
                'description' => t('Идентификатор в CRM B24'),

            )),
        ));
    }
}
