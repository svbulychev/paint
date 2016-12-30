<?
    use Paint\PaintService;

    class DetailPaintComponent extends \CBitrixComponent {

        public function executeComponent()
        {
            $elementId = $this->arParams['ELEMENT_ID'];
            $this->arResult['LIST_LINK'] = $this->arParams['FOLDER'] . $this->arParams['URL_TEMPLATES']['list'];
            $this->arResult['ADD_LINK'] = $this->arParams['FOLDER'] . $this->arParams['URL_TEMPLATES']['new'];
            $this->arResult['EDIT_BASE_LINK'] = $this->arParams['FOLDER'] . $this->arParams['URL_TEMPLATES']['edit'];
            $this->arResult['paint'] = false;

            $paintService = PaintService::getInstance();

            if (!$elementId) {
                $this->arResult['paint'] = false;
            } elseif ($paint = $paintService->getById($elementId)) {
                $this->arResult['paint'] = $paint;
                $this->arResult['EDIT_LINK'] = str_replace('#id#', $elementId, $this->arResult['EDIT_BASE_LINK']);
            }

            $this->includeComponentTemplate();
            return;
        }
    }