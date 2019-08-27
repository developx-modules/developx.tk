<?php
namespace Developx\Tk\DB;
use Bitrix\Main\Entity;

class PointsTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'developx_tkpoints';
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
            new Entity\StringField('ADR'),
            new Entity\StringField('PHONE'),
            new Entity\StringField('WORK_TIME'),
            new Entity\StringField('COORD'),
            new Entity\StringField('TK_ID')
        );
    }
}
