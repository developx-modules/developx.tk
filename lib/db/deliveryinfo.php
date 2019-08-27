<?php
namespace Developx\Tk\DB;
use Bitrix\Main\Entity;

class DeliveryInfoTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'developx_deliveryinfo';
    }

    public static function getMap()
    {
        return array(
            new Entity\IntegerField('ID', array(
                'autocomplete' => true,
                'primary' => true,
            )),
            new Entity\StringField('LOC_ID'),
            new Entity\StringField('TK'),
            new Entity\StringField('TIME'),
            new Entity\StringField('PRICE'),
        );
    }
}
