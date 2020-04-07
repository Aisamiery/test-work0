<?php
/**
 * Created by PhpStorm.
 * Author: Shabalin Pavel
 * Email: aisamiery@gmail.com
 * Date: 24.02.2020
 */
declare(strict_types=1);

namespace Afonay;


use Afonay\Entity\OrderRipeIpTable;
use Afonay\Model\OrderRipeIp;
use Afonay\Ripe\Request;
use Bitrix\Main\Application;
use Bitrix\Main\Event;
use Bitrix\Sale\Order;

class EventListener
{
    public static function onSavedOrder(Event $event): bool
    {
        try {
            $isNew = $event->getParameter('IS_NEW');
            /** @var Order $order */
            $order = $event->getParameter('ENTITY');

            // Проверяем только при создании заказа, при обновлении заказа менять IP нет смысла
            if ($isNew && $order instanceof Order) {
                // Ищем такой заказ
                $orderRipeIp = OrderRipeIpTable::getList(['select' => ['*'], 'filter' => ['=ORDER_ID' => $order->getId()]])->fetchObject();

                if ($orderRipeIp instanceof OrderRipeIp) {
                    // Такая запись к заказу уже существует
                    return true;
                }

                // Получить реальный IP адрес пользователя @todo упрощенная версия для тех задания
                $ip = $_SERVER["REMOTE_ADDR"];

                // Возможно данные об IP уже есть, дергать ripe нет смысла
                $orderRipeIp = OrderRipeIpTable::getList(['select' => ['*'], 'filter' => ['=IP_ADDRESS' => $ip]])->fetchObject();

                if ($orderRipeIp instanceof OrderRipeIp) {
                    $dataIpAddress = $orderRipeIp->getPayloadData();
                } else {
                    // Запросим данные от RIPE
                    $response = (new Request())->getAddressInfo($ip);
                    $dataIpAddress = $response->getObject();
                }

                $object = new OrderRipeIp();
                $object->setOrderId($order->getId());
                $object->setIpAddress($ip);
                $object->setPayloadData($dataIpAddress);
                $object->save();
            }
        } catch (\Exception $exception) {
            Application::getInstance()->getExceptionHandler()->writeToLog($exception);
        }

        return true;
    }
}