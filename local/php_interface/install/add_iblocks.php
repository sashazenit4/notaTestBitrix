<?php
require_once $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php";
/**
 * This file contain iblock data
 */

use Bitrix\Iblock;
use Bitrix\Main\Config\Option;
use Aholin\Tools\Console;

/**
 * Добавление типа инфоблока
 */

$arIblockTypes = [
    [
        "ID" => "reference",
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

    Option::set("a-holin", "REF_" . $arIblock["CODE"] . "_IBLOCK_ID", $iblockId);
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
        "ELEMENTS" => [
            [
                'NAME' => 'Тестовое сообщение',
                'CODE' => 'TEST_MESSAGE'
            ],
        ]
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
$obIblockElement = new \CIBlockElement();

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

    Option::set("a-holin", "REF_" . $arIblock["CODE"] . "_IBLOCK_ID", $iblockId);

    /**
     * Create section sections
     */
    foreach ($arIblock["SECTIONS"] as $arSection) {
        loadSection($iblockId, 0, $arSection);
    }

    $arExistedElements = [];

    $elements = Iblock\ElementTable::getList([
        'select' => [
            'ID',
            'CODE'
        ],
        'filter' => [
            '=IBLOCK_ID' => $iblockId
        ]
    ]);

    foreach ($elements as $element) {
        $arExistedElements[$element['CODE']] = $element['ID'];
    }

    $arExistedElementKeys = array_keys($arExistedElements);

    foreach ($arIblock["ELEMENTS"] as $arElement) {
        if (in_array($arElement['CODE'], $arExistedElementKeys)) {
            // continue;
            // Delete elements if DELETE
            if (! empty($arElement['DELETE']) && $arElement['DELETE']) {
                $result = $obIblockElement->Delete($arExistedElements[$arElement['CODE']]);
                if (! $result) {
                    Console::write("Элемент с ID={$arExistedElements[$arElement['CODE']]} CODE={$arElement['CODE']} не был удален. Ошибка: {$obIblockElement->LAST_ERROR}");
                }
                continue;
            }
            // Update elements
            $result = $obIblockElement->Update($arExistedElements[$arElement['CODE']], [
                "NAME" => $arElement['NAME'],
                "CODE" => $arElement['CODE'],
                "SORT" => isset($arElement['SORT']) ? $arElement['SORT'] : '',
                "ACTIVE" => "Y"
            ]);
            if (! $result) {
                Console::write("Ошибка обновления элемента с ID={$arExistedElements[$arElement['CODE']]}, {$obIblockElement->LAST_ERROR}", 'red');
            }
            else
            {
                Console::write("Обновлен элемент ID={$arExistedElements[$arElement['CODE']]}", 'cyan');
            }

            continue;
        }
        if (empty($arElement['DELETE'])) {

            $arFields = [
                "IBLOCK_ID" => $iblockId,
                "NAME" => $arElement["NAME"],
                "CODE" => $arElement["CODE"]
            ];

            $elementId = $obIblockElement->Add($arFields);

            if (! $elementId) {
                Console::write("Не удалось добавить элемент " . $arElement["NAME"] . " в инфоблок: " . $obIblockElement->LAST_ERROR, 'red');
                continue;
            }
        }
    }
}

function loadSection($iblock, $parentSectionId, $arSection)
{
    if (empty($arSection)) {
        return [];
    }

    $iSectionId = 0;

    $section = Iblock\SectionTable::getRow([
        'select' => [
            'ID',
            'CODE'
        ],
        'filter' => [
            '=IBLOCK_ID' => $iblock,
            '=CODE' => $arSection['CODE']
        ]
    ]);

    if ($section) {
        $iSectionId = $section['ID'];
        Console::write("Раздел " . $section["CODE"] . " уже есть");
    } else {
        $iblockSection = new CIBlockSection();

        $arFields = [
            'ACTIVE' => 'Y',
            'IBLOCK_ID' => $iblock,
            'IBLOCK_SECTION_ID' => $parentSectionId,
            "NAME" => $arSection['NAME'],
            "CODE" => $arSection['CODE'],
            "SORT" => $arSection['SORT']
        ];

        $iSectionId = $iblockSection->Add($arFields, true, false);

        if (empty($iSectionId)) {
            Console::write("Не удалось добавить элемент " . $arSection["NAME"] . " в инфоблок: " . $iblockSection->LAST_ERROR, 'red');
            return [];
        } else {
            Console::write("Раздел " . $section["CODE"] . " добавлен: " . $iSectionId);
        }
    }

    foreach ($arSection["SECTIONS"] as $arSect) {
        loadSection($iblock, $iSectionId, $arSect);
    }
}
