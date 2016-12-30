<?php

namespace Paint\Orm;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\DatetimeField;
use Bitrix\Main\Entity\Event;
use Bitrix\Main\Entity\EventResult;
use Bitrix\Main\Entity\IntegerField;
use Bitrix\Main\Entity\StringField;
use Bitrix\Main\Type\DateTime;

class PaintTable extends DataManager
{
    public static function getTableName()
    {
        return 'b_paint';
    }

    public static function getMap()
    {
        return [
            new IntegerField('id', [
                'primary' => true,
                'autocomplete' => true,
            ]),
            new StringField('access',[
                'required' => true,
            ]),
            new StringField('path', [
                'required' => 'true',
            ]),
//            new StringField('preview', [
//                'required' => 'true',
//            ]),
            new DateTimeField('created', [
                'default_value' => new DateTime()
            ]),
            new DateTimeField('updated', [
                'default_value' => new DateTime()
            ]),
        ];
    }

    public static function onBeforeUpdate(Event $event)
    {
        $eventResult = new EventResult();
        $eventResult->modifyFields(['updated' => new DateTime()]);

        return $eventResult;
    }

    public static function findAll() {
        $data = [];
        $dbResult = self::getList();
        while ($item = $dbResult->fetch()) {
            $data[] = $item;
        }

        return $data;
    }
}