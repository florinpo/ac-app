<?php

/**
 * Class for render Statistics * 
 * @package common.blocks.statistics */
class StatisticsBlock extends CWidget {

    //Do not delete these attr block, page and errors
    public $id = 'statistics';
    public $block = null;
    public $errors = array();
    public $page = null;
    public $layout_asset = '';
    private $_cs;

    public function setParams($params) {
        return;
    }

    public function run() {
        $this->renderContent();
    }

    protected function renderContent() {
        if (isset($this->block) && ($this->block != null)) {
            //Start working with Statistics here
            $params = b64_unserialize($this->block['params']);
            $this->setParams($params);
            $this->registerLayout();
            $data = array();
            
                $gaEmail = app()->params['ga_email'];
                $gaPassword = app()->params['ga_password'];
                $profileId = app()->params['ga_profile_id'];
                $dimensions = array('date','pageTitle', 'customVarValue1', 'customVarValue2');
                $metrics = array('visits', 'pageviews', 'visitors');
               
                $startIndex = 1;
                $maxResults = 1000;
                $ga = new gapi($gaEmail, $gaPassword);
                $filter = 'ga:customVarValue1==viettasrl';
                
                $date_from = isset($_GET['date-from']) ? $_GET['date-from'] : '28-01-2014';
                $date_to = isset($_GET['date-to']) ? $_GET['date-to'] : '04-02-2014';
            
          
                
                $sortMetric = array('date');
                
                $startDate = date("Y-m-d", strtotime($date_from));
                $endDate = date("Y-m-d", strtotime($date_to));
                
                if(isset($_GET['q']) && $_GET['q'] != '') {
                    $filter .= '&&pageTitle=@'.$_GET['q'];
                }
                
                $ga->requestReportData($profileId, $dimensions, $metrics, $sortMetric, $filter, $startDate, $endDate, $startIndex, $maxResults);


                $results = $ga->getResults();

                foreach ($results as $result) {

                    $id = $result->getCustomVarValue2();
                    $title = $result->getPageTitle();
                    $visits = $result->getPageViews();

                    $data[] = array('id' => $id, 'title' => $title, 'visits' => $visits);
                }
            

            $dataProvider = new CArrayDataProvider($data, array(
                        'pagination' => array(
                            'pageSize' => 3,
                        ),
                    ));

            $total = $dataProvider->getTotalItemCount();
            $pages = $dataProvider->getPagination();



            if ((Yii::app()->request->isAjaxRequest && isset($_GET['ajax']) && $_GET['ajax'] === 'statistics-grid')) {
                // Render partial file created in Step 1
                $this->getController()->renderPartial('common.blocks.statistics._grid', array(
                    'dataProvider' => $dataProvider,
                    'total' => $total,
                    'pageSize' => 3
                ));
                Yii::app()->end();
            }
            
            
            $this->render(BlockRenderWidget::setRenderOutput($this), array(
                'dataProvider' => $dataProvider,
                'total' => $total,
                'pages' => $pages,
                'pageSize' => 3
                ));
        } else {
            echo '';
        }
    }

    /*
     * this function is for list view registration layout
     */

    public function registerLayout() {
        $this->layout_asset = GxcHelpers::publishAsset(Yii::getPathOfAlias('common.layouts.default.assets'));
        $this->_cs = Yii::app()->getClientScript();
        $this->registerConfig();
        $this->_cs->registerCssFile($this->layout_asset . '/css/statistics/statistics-main.css');
        $this->_cs->registerScriptFile($this->layout_asset . '/js/plugins/jquery.infieldlabel.min.js', CClientScript::POS_END);
        $this->_cs->registerScriptFile($this->layout_asset . '/js/plugins/charts/jquery.flot.min.js', CClientScript::POS_END);
        $this->_cs->registerScriptFile($this->layout_asset . '/js/plugins/charts/jquery.flot.time.min.js', CClientScript::POS_END);
        //$this->_cs->registerScriptFile($this->layout_asset . '/js/plugins/charts/jquery.flot.selection.custom.js', CClientScript::POS_END);
        //$this->_cs->registerScriptFile($this->layout_asset . '/js/plugins/charts/jquery.flot.navigate.js', CClientScript::POS_END);
        $this->_cs->registerScriptFile($this->layout_asset . '/js/charts/statistics-main.js', CClientScript::POS_END);
    }

    public function registerConfig() {
        $notification = '';
        $notificationHeader = '';
        if (user()->hasFlash('info-ajax')) {
            $notification = user()->getFlash('info-ajax');
            $notificationHeader = t('site', 'Attenzione!');
        }

        $csrf = Yii::app()->getRequest()->getCsrfToken();

        $controllerUrl = app()->createUrl('statistics');

        $closeLabel = t('site', 'hide all notifications');
        $yesLabel = t('site', 'Si');
        $noLabel = t('site', 'No');

        // dialog labels
        $cancelLabel = t("site", "Cancela");
        $cancelDialogLabel = t("site", "Cancel");
        $okDialogLabel = t("site", "Yes, delete");

        $monthNamesShort = json_encode(GxcHelpers::monthNamesShort());
        $dateFrom = '01-11-2013'; // suppose this will be the date since the store is available
// set vars for javascript
        $js = <<<EOD
\$.statistics = {
     monthNamesShort: '{$monthNamesShort}',
     dateFrom: '{$dateFrom}',
     controllerUrl: '{$controllerUrl}',
     csrf: '{$csrf}'
};
EOD;
        $this->_cs->registerScript('statistics-js', $js, CClientScript::POS_HEAD);
    }

    public function validate() {
        return true;
    }

    public function params() {
        return array();
    }

    public function beforeBlockSave() {
        return true;
    }

    public function afterBlockSave() {
        return true;
    }

}

?>