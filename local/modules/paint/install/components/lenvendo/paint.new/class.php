<?
use Bitrix\Main\Application;
use \Bitrix\Main\Context;
use Paint\PaintService;

class PaintNewComponent extends \CBitrixComponent {

    public function executeComponent()
    {
        $this->arResult['LIST_LINK'] = $this->arParams['FOLDER'] . $this->arParams['URL_TEMPLATES']['list'];
        $detailBaseLink = $this->arResult['DETAIL_BASE_LINK'] = $this->arParams['FOLDER'] . $this->arParams['URL_TEMPLATES']['detail'];
        $newLink = $this->arParams['FOLDER'] . $this->arParams['URL_TEMPLATES']['new'];

        $cacheTag = $this->arParams['CACHE_TAG'];

        $request = Context::getCurrent()->getRequest();

        if($request->isAjaxRequest()) {

            $base64image = $request->getPost('image');
            $pass = $request->getPost('pass');

            $response = ['success' => false];

            try {
                $paint = PaintService::getInstance()->addPaint($pass, $base64image);

                if ($paint) {

                    $response = [
                        'success' => true,
                        'id' => $paint->getId(),
                        'location' => str_replace('#id#', $paint->getId(), $detailBaseLink),
                        'message' => 'Saved',
                    ];

                    Application::getInstance()->getTaggedCache()->clearByTag($cacheTag);

                } else {
                    $response['error'] = 'Unknown error';
                }
            } catch (Exception $e) {
                $response['error'] = $e->getMessage();
            }

            $GLOBALS['APPLICATION']->RestartBuffer();
            echo json_encode($response);
            die();

        } else {
            $this->arResult['NEW_LINK'] = $newLink;
            $this->includeComponentTemplate();
        }
    }
}