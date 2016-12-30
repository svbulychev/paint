<?
    if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
        die();
    }

    class PaintComponent extends \CBitrixComponent {

        protected $arDefaultUrlTemplates = [];

        protected $arUrlTemplates = [];

        protected $page = '';

        protected $arVariables = [];

        protected function setDefaultParams()
        {
            $this->arDefaultUrlTemplates = [
                "list" => "",
                "edit" => "edit/#id#",
                "new"  => "new",
                'detail' => 'view/#id#',
            ];

            $this->arUrlTemplates = [];
            $this->arVariables = ['id'];

            $this->setDefaultPage();
        }

        protected function setDefaultPage() {
            $this->page = 'list';
        }

        public function executeComponent()
        {
            $this->setDefaultParams();

            $arVariables = [];
            $arVariableAliases = [];
            if ($this->arParams['SEF_MODE'] == 'Y') {

                $this->arUrlTemplates = CComponentEngine::MakeComponentUrlTemplates(
                    $this->arDefaultUrlTemplates,
                    $this->arParams['SEF_URL_TEMPLATES']
                );

                $arVariableAliases = CComponentEngine::MakeComponentVariableAliases(
                    $this->arDefaultUrlTemplates,
                    $this->arParams['VARIABLE_ALIASES']
                );

                $engine = new CComponentEngine($this);
                $this->page = $engine->guessComponentPath(
                    $this->arParams['SEF_FOLDER'],
                    $this->arUrlTemplates,
                    $arVariables
                );

                if (!$this->page) {
                    $this->setDefaultPage();
                }

                CComponentEngine::InitComponentVariables(
                    $this->page,
                    $this->arVariables,
                    $arVariableAliases,
                    $arVariables
                );

            }

            $this->arResult = [
                'FOLDER' => $this->arParams['SEF_FOLDER'],
                'URL_TEMPLATES' => $this->arUrlTemplates,
                'VARIABLES' => $arVariables,
                'ALIASES' => $arVariableAliases,
                'CACHE_TIME' => $this->arParams['CACHE_TIME'] ? : 3600,
                'CACHE_TAG' => $this->arParams['CACHE_TAG'],
            ];

            $this->includeComponentTemplate($this->page);
        }
    }