<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Result;
use Bitrix\Main\Context;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\PageNavigation;
use Otus\Clinic\Utils\BaseUtils;
use Otus\Clinic\Services\DoctorService;
use Otus\Clinic\Helpers\IblockHelper;

class ClinicList extends CBitrixComponent
{
	public function executeComponent(): void
	{
        self::$grid = new Bitrix\Main\Grid\Options(self::GRID_ID);
        $request = Context::getCurrent()->getRequest();

        if (!Loader::includeModule('otus.clinic')) {
            throw new \RuntimeException(Loc::getMessage('ERROR_NOT_INCLUDE_MODULE'));
        }

        Loc::loadMessages(__FILE__);

        self::$iblockEntityId = Option::get('otus.clinic', 'OTUS_CLINIC_IBLOCK_DOCTORS');

        if (!intval(self::$iblockEntityId)) {
            throw new \RuntimeException(Loc::getMessage('ERROR_FATAL_IBL_ID_NULL'));
        }

        self::$referencePropCode = Option::get('otus.clinic', 'OTUS_CLINIC_IBLOCK_PROP_REFERENCE');

		self::$fields = IblockHelper::prepareFields($this->arParams['LIST_FIELD_CODE']);

		self::$properties = array_filter($this->arParams['LIST_PROPERTY_CODE']);

		$fieldsAndProperties = array_merge(self::$fields, self::$properties);

		$names = self::getNames();

		$gridHeaders = self::prepareHeaders($names);

		$gridFilterFields = self::prepareFilterFields($fieldsAndProperties, $names);

		$gridSortValues = self::prepareSortParams($fieldsAndProperties);

		$gridFilterValues = self::prepareFilterParams($gridFilterFields, $fieldsAndProperties);

        // Page navigation
        $gridNav = self::$grid->GetNavParams();
        $pager = new PageNavigation('page');
        $pager->setPageSize($gridNav['nPageSize']);
        $pager->setRecordCount(DoctorService::getCount($gridFilterValues));
        if ($request->offsetExists('page')) {
            $currentPage = $request->get('page');
            $pager->setCurrentPage($currentPage > 0 ? $currentPage : $pager->getPageCount());
        } else {
            $pager->setCurrentPage(1);
        }

		$doctors = DoctorService::getDoctors(
            [
                'select' => self::prepareSelectParams(),
                'filter' => self::prepareProperties($gridFilterValues),
                'sort' => self::prepareProperties($gridSortValues),
                'limit' => $pager->getLimit(),
                'offset' => $pager->getOffset(),
            ],
            self::$fields,
            self::$properties,
            self::$iblockEntityId
        );

        if (!$doctors->isSuccess()) {
            throw new \RuntimeException(BaseUtils::extractErrorMessage($doctors));
        }

		$rows = self::getRows($doctors, $fieldsAndProperties);

        if (!$rows->isSuccess()) {
            throw new \RuntimeException(BaseUtils::extractErrorMessage($rows));
        }

        $gridHeaders = [];
        $gridRows = [];
        $gridSort = [];
        $gridFilter = [];

		$this->arResult = [
			'GRID_ID' => 'customtab_sidepanel_handler',
			'HEADERS' => $gridHeaders,
			'ROWS' => $gridRows,
			'SORT' => $gridSort,
			'FILTER' => $gridFilter,
			'ENABLE_LIVE_SEARCH' => false,
			'DISABLE_SEARCH' => true,
            'PAGINATION' => array(
                'PAGE_NUM' => $pager->getCurrentPage(),
                'ENABLE_NEXT_PAGE' => $pager->getCurrentPage() < $pager->getPageCount(),
                'URL' => $request->getRequestedPage(),
            ),
		];

		$this->IncludeComponentTemplate();
	}

	private function getRows(): Result
	{

	}

}
