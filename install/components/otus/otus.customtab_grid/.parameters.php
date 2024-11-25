<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Bitrix\Iblock\TypeTable;
use Bitrix\Iblock\IblockTable;
use Otus\Clinic\Helpers\IblockHelper;

if (!Loader::includeModule('iblock')) {
	return;
}

if (!Loader::includeModule('otus.clinic')) {
    return;
}
$iblockId = Option::get('otus.clinic', 'OTUS_CLINIC_IBLOCK_DOCTORS');

$pops = IblockHelper::getIblockProps($iblockId);
$arPropertys = $pops->getData();

echo '<pre>';
var_dump($arPropertys);
echo '<pre>';

$arComponentParameters = [
	'GROUPS' => [
        'LIST' => [
            'NAME' => GetMessage('T_PARAMS_GRID'),
            'SORT' => 300
        ]
    ],
	'PARAMETERS' => [
        'SET_PAGE_TITLE' => [
            "PARENT" => "LIST",
            "NAME" => GetMessage("T_SET_PAGE_TITLE"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ],
        'SHOW_ROW_CHECKBOXES' => [
            "PARENT" => "LIST",
            "NAME" => GetMessage("T_SHOW_ROW_CHECKBOXES"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
        ],
        'NUM_PAGE' => [
            "PARENT" => "LIST",
            "NAME" => GetMessage("T_NUM_PAGE"),
            "TYPE" => "STRING",
            "DEFAULT" => 20,
        ],
		'CACHE_TIME' => ['DEFAULT' => 86400],
	],
];