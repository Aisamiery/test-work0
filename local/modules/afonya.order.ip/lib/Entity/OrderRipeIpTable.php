<?php
/**
 * Created by PhpStorm.
 * Author: Shabalin Pavel
 * Email: aisamiery@gmail.com
 * Date: 24.02.2020
 */
declare(strict_types=1);

namespace Afonay\Entity;


use Afonay\Model\OrderRipeIp;
use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\Entity\Validator\Length;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Sale\Internals\OrderTable;

class OrderRipeIpTable extends DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName(): string
    {
        return 'a_order_ripe_ip';
    }

    /**
     * Returns entity map definition.
     *
     * @return array
     */
    public static function getMap(): array
    {
        return [
            'ID' => [
                'data_type' => 'integer',
                'primary' => true,
                'autocomplete' => true,
                'title' => Loc::getMessage('RIPE_IP_ENTITY_ID_FIELD'),
            ],
            'ORDER_ID' => [
                'data_type' => 'integer',
                'required' => true,
                'title' => Loc::getMessage('RIPE_IP_ENTITY_ORDER_ID_FIELD'),
            ],
            'IP_ADDRESS' => [
                'data_type' => 'string',
                'required' => true,
                'validation' => [__CLASS__, 'validateIpAddress'],
                'title' => Loc::getMessage('RIPE_IP_ENTITY_IP_ADDRESS_FIELD'),
            ],
            'PAYLOAD' => [
                'data_type' => 'text',
                'required' => true,
                'title' => Loc::getMessage('RIPE_IP_ENTITY_PAYLOAD_FIELD'),
            ],
            'ORDER' => new ReferenceField(
                'ORDER',
                OrderTable::class,
                ['=this.ORDER_ID' => 'ref.ID'],
                ['join_type' => 'LEFT']
            )
        ];
    }

    /**
     * Returns validators for IP_ADDRESS field.
     *
     * @return array
     * @throws \Bitrix\Main\ArgumentTypeException
     */
    public static function validateIpAddress(): array
    {
        return [
            new Length(null, 15),
        ];
    }

    /**
     * @return string
     */
    public static function getObjectClass(): string
    {
        return OrderRipeIp::class;
    }
}