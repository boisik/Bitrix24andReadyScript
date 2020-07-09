<?php

namespace CrmB24\Config;

use RS\Event\HandlerAbstract;
use RS\Router\Route as RouterRoute;

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
        $this->bind('getroute');  //событие сбора маршрутов модулей
        $this->bind('getmenus'); //событие сбора пунктов меню для административной панели
    }

    /**
     * Возвращает маршруты данного модуля. Откликается на событие getRoute.
     * @param array $routes - массив с объектами маршрутов
     * @return array of \RS\Router\Route
     */
    public static function getRoute(array $routes)
    {
        $routes[] = new RouterRoute('crmb24-front-ctrl', [
            '/testmodule-crmb24/',
        ], null, 'Роут модуля CrmB24');

        return $routes;
    }

    /**
     * Возвращает пункты меню этого модуля в виде массива
     * @param array $items - массив с пунктами меню
     * @return array
     */
    public static function getMenus($items)
    {
        $items[] = [
            'title' => 'Пункт модуля CrmB24',
            'alias' => 'crmb24-control',
            'link' => '%ADMINPATH%/crmb24-control/',
            'parent' => 'modules',
            'sortn' => 40,
            'typelink' => 'link',
        ];
        return $items;
    }
}
