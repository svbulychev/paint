<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");?>
<?
    CModule::IncludeModule('paint');

    $APPLICATION->IncludeComponent('lenvendo:paint', '', [
        'SEF_MODE' => 'Y',
        'SEF_FOLDER' => '/paint/',
        'SEF_URL_TEMPLATES' => [],
        'VARIABLE_ALIASES' => [],
        'CACHE_TIME' => 3600,
        'CACHE_TAG' => 'paint',
    ]);
?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>