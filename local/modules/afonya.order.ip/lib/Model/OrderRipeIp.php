<?php
/**
 * Created by PhpStorm.
 * Author: Shabalin Pavel
 * Email: aisamiery@gmail.com
 * Date: 24.02.2020
 */
declare(strict_types=1);

namespace Afonay\Model;


use Afonay\Entity\OrderRipeIpTable;
use Bitrix\Main\ORM\Objectify\EntityObject;

/**
 * Class OrderRipeIp
 * @package Afonay\Model
 *
 * @method string getIpAddress()
 * @method void setIpAddress(string $ip)
 * @method int getOrderId()
 * @method void setOrderId(int $id)
 */
class OrderRipeIp extends EntityObject
{
    public static $dataClass = OrderRipeIpTable::class;

    /**
     * @return array|null
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\SystemException
     */
    public function getPayloadData(): ?array
    {
        return unserialize($this->get('PAYLOAD'));
    }

    public function setPayloadData(array $payload): void
    {
        $this->set('PAYLOAD', serialize($payload));
    }
}