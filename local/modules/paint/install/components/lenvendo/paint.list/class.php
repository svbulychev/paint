<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die(); ?>

<?
use Bitrix\Main\Application;
use Bitrix\Main\Data\Cache;
use Paint\PaintService;

class PaintListComponent extends \CBitrixComponent {

    public function executeComponent()
    {

        $this->arResult['EDIT_BASE_LINK'] = $this->arParams['FOLDER'] . $this->arParams['URL_TEMPLATES']['edit'];
        $this->arResult['ADD_LINK'] = $this->arParams['FOLDER'] . $this->arParams['URL_TEMPLATES']['new'];
        $this->arResult['DETAIL_BASE_LINK'] = $this->arParams['FOLDER'] . $this->arParams['URL_TEMPLATES']['detail'];

        $cacheId = md5(serialize($this->arParams));
        $cacheDir = '/paint';
        $cacheTime = $this->arParams['CACHE_TIME'] ? : 3600;

        $cache = Cache::createInstance();

        if ($cache->initCache($cacheTime, $cacheId, $cacheDir)) {
            $this->arResult['items'] = $cache->getVars();
        } elseif ($cache->startDataCache()) {

            $cacheManager = Application::getInstance()->getTaggedCache();
            $cacheManager->startTagCache($cacheDir);

            $this->arResult['items'] = $entities = PaintService::getInstance()->getAll();

            $cacheManager->registerTag($this->arParams['CACHE_TAG']);
            $cacheManager->endTagCache();

            $cache->endDataCache($entities);

        } else {
            $this->arResult['items'] = [];
        }

        $this->includeComponentTemplate();
    }
}