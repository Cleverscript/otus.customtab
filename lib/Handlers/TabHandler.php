<?php

namespace Otus\Customtab\Handlers;

use Bitrix\Main\Loader;
use Bitrix\Main\Event;
use Bitrix\Main\EventResult;

Loader::includeModule('crm');

class TabHandler
{
    public static function addTabs(Event $event): EventResult
    {
        $entityId = $event->getParameter('entityID');
        $entityTypeID = $event->getParameter('entityTypeID');
        $tabs = $event->getParameter('tabs');

        $canUpdateDeal = \CCrmDeal::CheckUpdatePermission(
            $entityId,
            \CCrmPerms::GetCurrentUserPermissions()
        );

        if ($canUpdateDeal) {
            $tabs = array_merge(
                $tabs,
                match (true) {
                    $entityTypeID === \CCrmOwnerType::Deal => self::addDealTabs(),
                    $entityTypeID === \CCrmOwnerType::Lead => self::addLeadTabs(),
                    $entityTypeID === \CCrmOwnerType::Company => self::addCompanyTabs(),
                    $entityTypeID === \CCrmOwnerType::Contact => self::addContactTabs(),
                }
            );
        }

        return new EventResult(EventResult::SUCCESS, [
            'tabs' => $tabs,
        ]);
    }

    private static function addDealTabs(): array
    {
        $tabs[] = [
            'id' => 'otus_customtab_deal',
            'name' => 'Заказы',
            'enabled' => true,
            'loader' => [
                'serviceUrl' => '/local/components/otus/otus.customtab_grid/lazyload.ajax.php?&site=' . \SITE_ID . '&' . \bitrix_sessid_get(),
                'componentData' => [
                    'template' => '',
                    'params' => [
                        "SET_PAGE_TITLE" => "N",
                        "COMPONENT_TEMPLATE" => ".default",
                        "SHOW_ROW_CHECKBOXES" => "Y",
                        "NUM_PAGE" => "5",
                        "CACHE_TYPE" => "A",
                        "CACHE_TIME" => "86400"
                    ]
                ]
            ]
        ];

        return $tabs;
    }

    private static function addLeadTabs(): array
    {
        return [];
    }

    private static function addCompanyTabs(): array
    {
        return [];
    }

    private static function addContactTabs(): array
    {
        return [];
    }
}
