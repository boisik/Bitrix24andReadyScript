<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace CrmB24\Model;

/**
 * Класс отвечает за вдение логов данного модуля
 */
class Log extends \RS\Helper\Log
{
    const
        LOG_FILE = '/logs/hatetrix.log.txt'; //storage/log/....

    protected static $log;

    /**
     * Инициализирует текущий класс во время автозагрузки
     */
    public static function staticInit()
    {
        $config = \RS\Config\Loader::byModule(__CLASS__);
        if ($config->enable_log && !self::$log) {
            self::$log = \RS\Helper\Log::file(self::getLogFilename());
            self::$log->enableDate(true);
        }
    }

    /**
     * Возвращает объект логировщика или null, если логирование отключено
     *
     * @return \RS\Helper\Log | null
     */
    public static function getLogInstance()
    {
        return self::$log;
    }

    /**
     * Добавляет одну строку в лог
     *
     * @param string $data
     * @return bool
     */
    public static function write($data)
    {
        if (self::$log) {
            self::$log->append($data);
            return true;
        }
        return false;
    }

    /**
     * Возвращает путь к лог-файлу для записи сообщений
     *
     * @return string
     */
    public static function getLogFilename()
    {
        return \Setup::$PATH.\Setup::$STORAGE_DIR.self::LOG_FILE;
    }
}

Log::staticInit();