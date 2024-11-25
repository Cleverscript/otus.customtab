<?php
use Bitrix\Main;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;

$module_id = "otus.clinic";

$defaultOptions = Option::getDefaults($module_id);

Loader::registerAutoLoadClasses(null, [
    //'Otus\Clinic\Models\AbstractIblockPropertyValuesTable' => "/local/modules/{$module_id}/lib/Models/AbstractIblockPropertyValuesTable.php",
]);