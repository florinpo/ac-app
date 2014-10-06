<?php

class StatisticsController extends FeController {

    /**
     * List of allowd default Actions for the user
     * @return type 
     */
    public function allowedActions() {
        return 'dayvisits';
    }

    /*
     * this function will return the number of visits from GA
     */
    
    public function actionDayvisits() {
        if (Yii::app()->request->isPostRequest) {
            $date_from = $_POST['date-from'];
            $date_to = $_POST['date-to'];
            $keyword = $_POST['q'];
            $ids = $_POST['ids'];

            $gaEmail = app()->params['ga_email'];
            $gaPassword = app()->params['ga_password'];
            $profileId = app()->params['ga_profile_id'];
            $dimensions = array('year', 'month', 'day', 'date', 'pageTitle', 'customVarValue1', 'customVarValue2');
            $metrics = array('visits', 'pageviews', 'visitors');
            $sortMetric = array('date');
            $filter = 'ga:customVarValue1=~viettasrl';
            if ($keyword != '') {
                $filter .= '&&pageTitle=@' . $keyword;
            }
            
            $startDate = date("Y-m-d", strtotime($date_from));
            $endDate = date("Y-m-d", strtotime($date_to));

            $startIndex = 1;
            $maxResults = 1000;

            $ga = new gapi($gaEmail, $gaPassword);
            $ga->requestReportData($profileId, $dimensions, $metrics, $sortMetric, $filter, $startDate, $endDate, $startIndex, $maxResults);

            $resultsByDayViews = array();
            $resultsSumViews = array();
            
            $resultsByDayVisits = array();
            $resultsSumVisits = array();
            
            $range = createDateRangeArray($startDate, $endDate);
            
            foreach ($range as $date_r) {
                $results = $ga->getResults();
                if (!empty($results)) {
                    foreach ($results as $result) {
                        $id = $result->getcustomVarValue2();
                        if (isset($ids) && in_array($id, $ids)) {
                            $views = $result->getPageViews();
                            $visits = $result->getVisits();
                            $date = $result->getYear() . "-" . $result->getMonth() . "-" . $result->getDay();
                            if ($date == $date_r) {
                                $resultsByDayViews[$date_r][] = $views;
                                $resultsByDayVisits[$date_r][] = $visits;
                            } else {
                                $resultsByDayViews[$date_r][] = 0;
                                $resultsByDayVisits[$date_r][] = 0;
                            }
                        }
                    }
                } else {
                    $resultsByDayViews[$date_r][] = 0;
                    $resultsByDayVisits[$date_r][] = 0;
                }
            }

            foreach ($resultsByDayViews as $k => $subr) {
                $resultsSumViews[] = array(strtotime($k . " UTC") * 1000, array_sum($subr));
            }
            foreach ($resultsByDayVisits as $k => $subr) {
                $resultsSumVisits[] = array(strtotime($k . " UTC") * 1000, array_sum($subr));
            }

            echo json_encode(array(
                'success' => 1,
                'views' => $resultsSumViews,
                'visits' => $resultsSumVisits
            ));

            app()->end();
        } else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }

}