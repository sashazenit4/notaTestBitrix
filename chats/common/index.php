<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Общий чат");
?><?$APPLICATION->IncludeComponent(
	"aholin:common.chat", 
	".default", 
	array(
		"COMPONENT_TEMPLATE" => ".default",
		"CHATS_IBLOCK_ID" => "11",
		"ELEMENT_PER_PAGE" => "20",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000"
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>