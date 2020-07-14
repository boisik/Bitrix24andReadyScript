<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace CrmB24\Controller\Admin;

use CrmB24\Model\Log;


/**
 * Контроллер обрабатывает системные инструменты модуля
 */
class Tools extends \RS\Controller\Admin\Front
{
    protected
        $log_file;

    function init()
    {
        $this->log_file = Log::getLogFilename();

    }



    /**
     * Отображает лог файл
     */
    function actionShowLog()
    {
        if (file_exists($this->log_file)) {
            echo '<pre>';
            readfile($this->log_file);
            echo '</pre>';
        } else {
            return t('Лог файл не найден');
        }
    }

    /**
     * Удаляет лог файл
     */
    function actionDeleteLog()
    {
        if (file_exists($this->log_file)) {
            unlink($this->log_file);
            return $this->result->setSuccess(true)->addMessage(t('Лог-файл успешно удален'));
        } else {
            return $this->result->setSuccess(true)->addEMessage(t('Лог-файл отсутствует'));
        }
    }


}