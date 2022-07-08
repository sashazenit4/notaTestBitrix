<?php
/**
 * This file contain iblock data
 */

CModule::IncludeModule('iblock');

use Bitrix\Iblock;
use Aholin\Tools\Console;

/**
 * Добавление типа инфоблока
 */

$arIblockTypes = [
    [
        "ID" => "chats",
        "SECTIONS" => "N",
        "IN_RSS" => "N",
        "SORT" => 10,
        "LANG" => [
            "ru" => [
                "NAME" => "Чаты"
            ],
            "en" => [
                "NAME" => "chats"
            ]
        ]
    ]
];

$iblockTypes = Iblock\TypeTable::getList([
    "select" => [
        "ID"
    ]
]);

$arExistedIblockTypes = [];
foreach ($iblockTypes as $iblockType) {
    $arExistedIblockTypes[] = $iblockType["ID"];
}

foreach ($arIblockTypes as $arIblockType) {
    if (in_array($arIblockType["ID"], $arExistedIblockTypes)) {
        Console::write("Тип инфоблока '" . $arIblockType["ID"] . "' существует", 'yellow');
        continue;
    }

    $obIblockType = new \CIBlockType();

    if ($obIblockType->add($arIblockType)) {
        Console::write("Добавлен тип инфоблока '" . $arIblockType["ID"] . "'", 'green');
    } else {
        Console::write("Не удалось добавить тип инфоблока '" . $arIblockType["ID"] . "'", 'red');
        die();
    }
}

/**
 * Добавление инфоблоков
 */

$arIblocks = 
[
    [
        "NAME" => "Общий чат",
        "CODE" => "COMMON_CHAT",
        "IBLOCK_TYPE_ID" => "chats",
        "LIST_PAGE_URL" => "#SITE_DIR#chats/common/index.php",
        "DETAIL_PAGE_URL" => "#SITE_DIR#chats/common/index.php?ID=#ID#",
    ],
];

$iblocks = \Bitrix\Iblock\IblockTable::getList([
    "select" => [
        "ID",
        "CODE",
        "IBLOCK_TYPE_ID"
    ],
    "filter" => [
        '=IBLOCK_TYPE_ID' => array_column($arIblocks, "IBLOCK_TYPE_ID")
    ]
]);

$arExistedIblocks = [];
foreach ($iblocks as $iblock) {
    $arExistedIblocks[$iblock["IBLOCK_TYPE_ID"]][$iblock["CODE"]] = $iblock["ID"];
}

$obIblock = new \CIBlock();

foreach ($arIblocks as $arIblock) {
    if (in_array($arIblock["CODE"], array_keys($arExistedIblocks[$arIblock["IBLOCK_TYPE_ID"]]))) 
    {
        Console::write("Инфоблок '[" . $arIblock["IBLOCK_TYPE_ID"] . "] " . $arIblock["NAME"] . "' уже существует", "yellow");

        $iblockId = $arExistedIblocks[$arIblock["IBLOCK_TYPE_ID"]][$arIblock["CODE"]];
        $arFieldsUpd = [
            "NAME" => $arIblock["NAME"],
            "CODE" => $arIblock["CODE"],
            "IBLOCK_TYPE_ID" => $arIblock["IBLOCK_TYPE_ID"],
            "LIST_PAGE_URL" => $arIblock["LIST_PAGE_URL"],
            "DETAIL_PAGE_URL" => $arIblock["DETAIL_PAGE_URL"],
            "ACTIVE" => "Y",
            "SITE_ID" => [
                "s1"
            ],
            "GROUP_ID" => [
                "2" => "R"
            ],
            'BIZPROC' => 'N',
            'WORKFLOW' => 'N'
        ];
        if($obIblock->Update($iblockId, $arFieldsUpd))
        {
            Console::write("Инфоблок '[" . $arIblock["IBLOCK_TYPE_ID"] . "] " . $arIblock["NAME"] . "' обновлен", "cyan");
        }
        else
        {
            Console::write("Инфоблок '[" . $arIblock["IBLOCK_TYPE_ID"] . "] " . $arIblock["NAME"] . ' ' . $obIblock->LAST_ERROR, 'red');
        }
    } else {

        $arFields = [
            "NAME" => $arIblock["NAME"],
            "CODE" => $arIblock["CODE"],
            "IBLOCK_TYPE_ID" => $arIblock["IBLOCK_TYPE_ID"],
            "LIST_PAGE_URL" => $arIblock["LIST_PAGE_URL"],
            "DETAIL_PAGE_URL" => $arIblock["DETAIL_PAGE_URL"],
            "ACTIVE" => "Y",
            "SITE_ID" => [
                "s1"
            ],
            "GROUP_ID" => [
                "2" => "R"
            ],
            'BIZPROC' => 'N',
            'WORKFLOW' => 'N'
        ];

        $iblockId = $obIblock->Add($arFields);

        if (! $iblockId) {
            Console::write("Не удалось добавить инфоблок " . $arIblock["NAME"] . ' ' . $obIblock->LAST_ERROR, 'red');
            die();
        }

        $arExistedIblocks[$arIblock["IBLOCK_TYPE_ID"]][$arIblock["CODE"]] = $iblockId;
        Console::write("Добавлен инфоблок " . $arIblock["NAME"], 'green');
    }
}