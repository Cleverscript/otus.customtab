<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Error;
use Bitrix\Main\Result;
use Bitrix\Main\Context;
use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\PageNavigation;
use Otus\Customtab\Models\OrderTable;

Loc::loadMessages(__FILE__);

class CustomtabGrid extends CBitrixComponent
{
    const GRID_ID = 'otus_customtab_grid';
	public function executeComponent(): void
	{
        try {
            $request = Context::getCurrent()->getRequest();

            if (!Loader::includeModule('otus.customtab')) {
                throw new \RuntimeException(Loc::getMessage('OTUS_CUSTOMTAB_FAIL_INCLUDE_MODULE'));
            }

            if (isset($request['order_list'])) {
                $page = explode('page-', $request['order_list']);
                $page = $page[1];
            } else {
                $page = 1;
            }

            // Page navigation
            $totalRowsCount = OrderTable::getCount();
            $nav = new \Bitrix\Main\UI\PageNavigation('order_list');
            $nav->allowAllRecords(false)->setPageSize($this->arParams['NUM_PAGE'])->initFromUri();
            $nav->setRecordCount($totalRowsCount);

            // Get grid options
            $gridOptions = new Bitrix\Main\Grid\Options(self::GRID_ID);
            $navParams = $gridOptions->GetNavParams();

            $gridColumns= self::getColumns();
            if (!$gridColumns->isSuccess()) {
                throw new \RuntimeException(implode(', ', $gridColumns->getErrorMessages()));
            }

            $limit = $this->arParams['NUM_PAGE']==$navParams['nPageSize']? $this->arParams['NUM_PAGE'] : $navParams['nPageSize'];
            $gridRows = self::getRows($page, $limit);

            if (!$gridRows->isSuccess()) {
                throw new \RuntimeException(implode(', ', $gridRows->getErrorMessages()));
            }

            $this->arResult = [
                'GRID_ID' => self::GRID_ID,
                'COLUMNS' => $gridColumns->getData(),
                'ROWS' => $gridRows->getData(),
                'NAV_OBJECT' => $nav,
                'TOTAL_ROWS_COUNT' => $totalRowsCount,
                'SHOW_ROW_CHECKBOXES' => $this->arParams['SHOW_ROW_CHECKBOXES'],
                'ALLOW_SORT' => true,
            ];

            $this->IncludeComponentTemplate();

        } catch (\Throwable $e) {
            ShowError($e->getMessage());
        }
	}

    private function getColumns(): Result
    {
        $result = new Result;
        $columns = [];

        $rows = OrderTable::getMap();

        foreach ($rows as $key => $field) {
            $id = $field->getName();

            switch($field->getName()) {
                case'COMPANY':
                case'CLIENT': {
                    $id = $id . '_NAME';
                    break;
                }
            }

            $columns[] = [
                'id' => $id,
                'name' => $field->getTitle(),
                'default' => true
            ];
        }

        return $result->setData($columns);
    }

	private function getRows(int $page = 1, int $limit): Result
	{
        $result = new Result;
        $data = [];
        $offset = $limit * ($page-1);

        $rows = OrderTable::query()
            ->setSelect([
                '*',
                'COMPANY_NAME' => 'COMPANY.NAME',
                'CLIENT_NAME' => 'CLIENT.NAME',
            ])
            ->addOrder('ID', 'DESC')
            ->setLimit($limit)
            ->setOffset($offset);

        //print_r($rows->getQuery());

        $rows = $rows->exec();

        if (empty($rows)) {
            return $result->addError(new Error(Loc::getMessage('OTUS_CUSTOMTAB_ORDERS_NOT_FOUNT')));
        }

        foreach ($rows as $row) {
            $data[] = [
                'id' => $row['ID'],
                'columns' => $row
            ];
        }

        return $result->setData($data);
	}
}
