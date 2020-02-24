<?php
/**
 * Created by PhpStorm.
 * Author: Shabalin Pavel
 * Email: aisamiery@gmail.com
 * Date: 24.02.2020
 */

global $DB, $APPLICATION;

define('ROOT_DIR', $_SERVER['DOCUMENT_ROOT']);
define('LOCAL_DIR', ROOT_DIR . '/local');
define('SIMPLEWINE_MODULE_DIR', LOCAL_DIR . '/modules/afonya.order.ip');

require_once (ROOT_DIR . '/bitrix/modules/main/admin_tools.php');
require_once (ROOT_DIR . '/bitrix/modules/main/filter_tools.php');
IncludeModuleLangFile(__FILE__);

CModule::AddAutoloadClasses('afonya.order.ip', [
    'Afonay\\EventListener' => 'lib/EventListener.php',
    'Afonay\\Entity\\OrderRipeIpTable' => 'lib/Entity/OrderRipeIpTable.php',
    'Afonay\\Model\\OrderRipeIp' => 'lib/Model/OrderRipeIp.php',
    'Afonay\\Ripe\\Request' => 'lib/Ripe/Request.php',
    'Afonay\\Ripe\\Response' => 'lib/Ripe/Response.php',
]);