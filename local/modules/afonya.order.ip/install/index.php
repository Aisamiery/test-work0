<?php
declare(strict_types=1);

class afonya_order_ip extends CModule
{
    const TABLE_NAME_IP = 'a_order_ripe_ip';

    public $MODULE_ID = 'afonya.order.ip';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $MODULE_CSS;

    public function __construct()
    {
        $arModuleVersion = array();

        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path."/version.php");

        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }

        $this->MODULE_NAME = "Афоня RIPE IP";
        $this->MODULE_DESCRIPTION = "Записывает информацию по IP адресу пользователя совершившего заказ";
    }

    public function DoInstall(): void
    {
        global $DOCUMENT_ROOT, $APPLICATION;

        RegisterModule($this->MODULE_ID);

        $connection = \Bitrix\Main\Application::getConnection();

        try {
            $connection->startTransaction();

            // Таблица для хранения данных по IP
            $fields = [
                'ID' => new \Bitrix\Main\ORM\Fields\IntegerField('ID'),
                'ORDER_ID' => new \Bitrix\Main\ORM\Fields\IntegerField('ORDER_ID', ['required' => true]),
                'IP_ADDRESS' => new \Bitrix\Main\ORM\Fields\StringField('IP_ADDRESS', ['required' => true, 'size' => 15]),
                'PAYLOAD' => new \Bitrix\Main\ORM\Fields\TextField('PAYLOAD', ['required' => true])
            ];

            $connection->createTable(self::TABLE_NAME_IP, $fields, ['ID'], ['ID']);

            // Добавим индексы - таблица будет большой
            $connection->queryExecute(sprintf('ALTER TABLE `%s` MODIFY `ORDER_ID` INT UNSIGNED NOT NULL UNIQUE', self::TABLE_NAME_IP));
            $connection->queryExecute(sprintf('ALTER TABLE `%s` MODIFY `IP_ADDRESS` VARCHAR(15) NOT NULL', self::TABLE_NAME_IP));

            $connection->createIndex(
                self::TABLE_NAME_IP,
                sprintf('idx_%s_ip', self::TABLE_NAME_IP),
                ['IP_ADDERSS']
            );

            $connection->commitTransaction();
        } catch (\Exception $exception) {
            $connection->rollbackTransaction();
            \Bitrix\Main\Application::getInstance()->getExceptionHandler()->writeToLog($exception);
        }

        // Регистрация события
        $eventManager = \Bitrix\Main\EventManager::getInstance();
        $eventManager->registerEventHandler('sale', 'OnSaleOrderSaved', $this->MODULE_ID, 'Afonay\\EventListener', 'onSavedOrder');

        $APPLICATION->IncludeAdminFile("Установка модуля {$this->MODULE_ID}", $DOCUMENT_ROOT."/local/modules/{$this->MODULE_ID}/install/step.php");
    }

    public function DoUninstall(): void
    {
        global $DOCUMENT_ROOT, $APPLICATION;

        UnRegisterModule($this->MODULE_ID);

        $connection = \Bitrix\Main\Application::getConnection();
        $connection->dropTable(self::TABLE_NAME_IP);

        // Открепим события
        UnRegisterModuleDependences('sale', 'OnSaleOrderSaved', $this->MODULE_ID, 'Afonay\\EventListener', 'onSavedOrder');

        $APPLICATION->IncludeAdminFile("Деинсталяция {$this->MODULE_ID}", $DOCUMENT_ROOT."/local/modules/{$this->MODULE_ID}/install/unstep.php");
    }
}