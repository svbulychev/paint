<?
use \Bitrix\Main\Context;
use Paint\PaintService;

class PaintEditComponent extends \CBitrixComponent {

    public function executeComponent()
    {
        $elementId = $this->arParams['ELEMENT_ID'];

        $this->arResult['LIST_LINK'] = $this->arParams['FOLDER'] . $this->arParams['URL_TEMPLATES']['list'];
        $this->arResult['EDIT_BASE_LINK'] = $this->arParams['FOLDER'] . $this->arParams['URL_TEMPLATES']['edit'];
        $this->arResult['ADD_LINK'] = $this->arParams['FOLDER'] . $this->arParams['URL_TEMPLATES']['new'];

        if (!$elementId) {
            $this->includeComponentTemplate('not_found');
            return;
        }

        $paint = PaintService::getInstance()->getById($elementId);
        if (!$paint) {
            $this->includeComponentTemplate('not_found');
            return;
        }

        $this->arResult['EDIT_LINK'] = str_replace('#id#', $elementId, $this->arResult['EDIT_BASE_LINK']);

        $request = Context::getCurrent()->getRequest();
        $paintService = PaintService::getInstance();

        $this->arResult['paint'] = $paint;
        $this->arResult['token'] = $paintService->generateToken($paint);

        //ajax
        if($request->isAjaxRequest()) {

            $base64image = $request->getPost('image');
            $response = ['success' => false];

            try {
                if ($paint) {
                    if ($paintService->updatePaint($paint, $base64image, $_SESSION['paints'])) {

                        $response = [
                            'success' => true,
                            'id' => $paint->getId(),
                            'message' => 'Saved',
                        ];
                    }

                } else {
                    $response['error'] = 'Not found';
                }
            } catch (Exception $e) {
                $response['error'] = $e->getMessage();
            }

            $GLOBALS['APPLICATION']->RestartBuffer();
            echo json_encode($response);
            die();

        } elseif ($request->isPost()) {

            $pass = $request->getPost('pass');
            $token = $request->getPost('token');

            if (!isset($_SESSION['paints'])) {
                $_SESSION['paints'] = [];
            }

            if ($paintService->verifyByPassword($paint, $pass, $token, $_SESSION['paints'])) {

                $this->arResult['view'] = 'edit';

                $this->includeComponentTemplate();
                return;

            } else {
                $this->arResult['error'] = 'Wrong password';
                $this->arResult['pass'] = $pass;
            }
        } else {

            if ($paintService->checkAccess($paint, $_SESSION['paints'])) {

                $this->arResult['view'] = 'edit';

                $this->includeComponentTemplate();
                return;
            }
        }

        $this->arResult['view'] = 'pass';
        $this->includeComponentTemplate();
    }
}