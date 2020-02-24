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

            // ��������� ������ ��� �������� ������, ��� ���������� ������ ������ IP ��� ������
            if ($isNew && $order instanceof Order) {
                // ���� ����� �����
                $orderRipeIp = OrderRipeIpTable::getList(['select' => ['*'], 'filter' => ['=ORDER_ID' => $order->getId()]])->fetchObject();

                if ($orderRipeIp instanceof OrderRipeIp) {
                    // ����� ������ � ������ ��� ����������
                    return true;
                }

                // �������� �������� IP ����� ������������ @todo ���������� ������ ��� ��� �������
                $ip = $_SERVER["REMOTE_ADDR"];

                // �������� ������ �� IP ��� ����, ������� ripe ��� ������
                $orderRipeIp = OrderRipeIpTable::getList(['select' => ['*'], 'filter' => ['=IP_ADDRESS' => $ip]])->fetchObject();

                if ($orderRipeIp instanceof OrderRipeIp) {
                    $dataIpAddress = $orderRipeIp->getPayloadData();
                } else {
                    // �������� ������ �� RIPE
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