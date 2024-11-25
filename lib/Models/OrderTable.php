<?php
namespace Otus\Customtab\Models;

use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Fields\Validators\LengthValidator;

Loc::loadMessages(__FILE__);

class OrderTable extends DataManager
{
    public static function getTableName()
    {
        return 'otus_order';
    }
    public static function getMap()
    {
        return [
            'ID' => (new IntegerField('ID'))
                ->configurePrimary(true)
                ->configureAutocomplete(true)
                ->configureTitle(Loc::getMessage('ID_NAME')),

            'TITLE' => (new StringField('TITLE',
                [
                    'validation' => function()
                    {
                        return[
                            new LengthValidator(null, 255),
                        ];
                    },
                ]
            ))->configureTitle(Loc::getMessage('TITLE_NAME')),

            'COMPANY_ID' => (new IntegerField(
                'COMPANY_ID'
            ))->configureTitle(Loc::getMessage('COMPANY_ID_NAME')),

            'CLIENT_ID' => (new IntegerField(
                'CLIENT_ID'
            ))->configureTitle(Loc::getMessage('CLIENT_ID_NAME')),

            'COMPANY' => (new Reference('COMPANY',
                \Bitrix\Iblock\Elements\ElementCompanyTable::class,
                Join::on('this.COMPANY_ID', 'ref.ID')
            ))->configureTitle(Loc::getMessage('COMPANY_NAME')),

            'CLIENT' => (new Reference('CLIENT',
                \Bitrix\Iblock\Elements\ElementClientsTable::class,
                Join::on('this.CLIENT_ID', 'ref.ID')
            ))->configureTitle(Loc::getMessage('CLIENT_NAME')),
        ];
    }
}