<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main;
use Bitrix\Main\Loader;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\UserTable;
use	Bitrix\Iblock;
use Bitrix\Main\Engine\Contract\Controllerable;

class CommonChat extends \CBitrixComponent implements Controllerable
{
    public function configureActions()
    {
        return [];
    }

    public function listKeysSignedParameters()
    {
        return [];
    }

    public function executeComponent()
	{
        if(!Loader::includeModule("iblock"))
        {
            ShowError(GetMessage("NOT_MODULE"));
            return;
        }

        $this->IncludeComponentTemplate();
    }

    public function getUserView($user)
    {
        return $user;
    }

    public function getMessages($iblockId)
    {
        $rsElement = CIBlockElement::GetList(
            $arOrder  = array("SORT" => "ASC"),
            $arFilter = array(
                "ACTIVE"    => "Y",
                "IBLOCK_ID" => $iblockId,
            ),
            false,
            false,
            $arSelectFields = array("ID", "NAME", "IBLOCK_ID", "CODE", "PROPERTY_SENDER", "DATE_CREATE")
        );

        $arElements = [];
        while ($arElement = $rsElement->Fetch()) {
            $arElements[] = $arElement;
        }
        return $arElements;
    }

    public function newMessageAction ($text, $fromUserId, $iblockId)
    {
        if(!Loader::includeModule("iblock"))
        {
            ShowError(GetMessage("NOT_MODULE"));
            return;
        }
        

        $obIblockElement = new \CIblockElement();

        $arFields = [
            "IBLOCK_ID" => $iblockId,
            "NAME" => $text,
        ];

        $dateCreate = (new DateTime())->format('d.m.Y H:i:s');

        $elementId = $obIblockElement->Add($arFields);

        $user = CUser::getList(
            "id",
            "asc",
            [
                "ID" => $fromUserId
            ]
        );

        $user = $user->Fetch();

        $userName = $user["LAST_NAME"] . " " . $user["NAME"];

        
        $obIblockElement->SetPropertyValues($elementId, $iblockId, $fromUserId, "SENDER");

        if ($elementId)
            return [
                "text" => $text,
                "fromUserId" => $userName, 
                "dateCreate" => $dateCreate
            ];
        else 
            return $obIblockElement->LAST_ERROR;
    }
}