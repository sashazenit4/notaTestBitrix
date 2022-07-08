<?php
/** Special constants */
define('NOT_CHECK_PERMISSIONS', true);
define('NO_KEEP_STATISTIC', true);

/** Environment params for CLI */
$_SERVER['DOCUMENT_ROOT'] = realpath('/home/bitrix/www');
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];

/** Prevent browser executing */
if (php_sapi_name() != 'cli') {
    die();
}

use \Aholin\Tools\Console;

/** Include bitrix core */
require_once $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php";

$arSteps = [
    'add_iblocks',
    'add_iblock_property',
];

foreach ($arSteps as $step) {
    $stageDir = realpath(__DIR__.'/steps/');
    $stepPath = $stageDir.'/'.$step.'.php';

    if (!file_exists($stepPath)) {
        Console::write("Exist record with step {$step} but file not found", 'red');

        continue;
    }

    try {
        require_once $stepPath;
    } catch (\Exception $e) {
        Console::write("During process '{$step}' step thrown error:".$e->getMessage(), 'red');
    }
}
