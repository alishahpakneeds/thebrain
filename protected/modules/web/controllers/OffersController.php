<?php

/**
 * 
 */
class OffersController extends Controller {

    /**
     * @return array action filters
     */
    public function filters() {
        return array(
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
        );
    }

    /**
     * Item model
     * @var type 
     * used in widget
     */
    public $item;

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules() {
        return array(
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array(
                    'post',
                    'addpartial',
                ),
                'users' => array('@'),
            ),
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array(
                    'category',
                    'search',
                    'calculatePrice',
                    'detail'
                ),
                'users' => array('*'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    /**
     * category search
     */
    public function actionCategory($category = "") {
        $cat_arr = explode("-", $category);
        $criteria = new CDbCriteria();
        $criteria->addCondition("group_id = " . $cat_arr[count($cat_arr) - 1]);
        $dataProvider = new CActiveDataProvider('BspItem', array(
            'criteria' => $criteria,
        ));
        $this->render("//offers/category", array(
            "cat_arr" => $cat_arr,
            "dataProvider" => $dataProvider
        ));
    }

    /**
     * search page
     */
    public function actionSearch() {
        $model = new OfferSearch;
        $criteria = new CDbCriteria();
        if (isset($_POST['OfferSearch'])) {
            $model->attributes = $_POST['OfferSearch'];
            $criteria->compare("name", $model->keyword);
            $criteria->compare("lat", $model->lat);
            $criteria->compare("lng", $model->lng);
            //new attribute
            $criteria->compare("special_deal", $model->special_deal);
            $with = array();
            if ($model->withVideo == 1) {
                $with = array(
                    'item_video' => array(
                        'joinType' => 'INNER JOIN',
                    ),
                );
            }
            if ($model->withSound == 1) {
                $with ['item_related_sounds'] = array(
                    'joinType' => 'INNER JOIN',
                );
            }
            if ($model->popularity == 1) {
                $with ['image_log'] = array(
                    'joinType' => 'INNER JOIN',
                );
            }
            if ($model->nearFirst == 1) {
                $criteria->order = "t.id ASC";
            }

            if (!empty($with)) {
                $criteria->with = $with;
            }

            if ($model->lowPrice == 1) {
                $criteria->addCondition("(discount_price IS NOT NULL OR discount_price !='')");
            }
            if ($model->highPrice == 1) {
                $criteria->addCondition("(price IS NOT NULL OR price !='')");
            }
        }
        $dataProvider = new CActiveDataProvider('BspItem', array(
            'criteria' => $criteria,
        ));

        if (isset($_GET['ajax'])) {
            $this->renderPartial("//offers/_search_result", array(
                "cat_arr" => array("0" => "", 1 => ""),
                "dataProvider" => $dataProvider
            ));
        } else {
            $this->render("//offers/search", array(
                "cat_arr" => array("0" => "", 1 => ""),
                "dataProvider" => $dataProvider
            ));
        }
    }

    /**
     * 
     * @param type $slug
     */
    public function actionDetail($slug = "") {
        $slug_arr = explode("-", $slug);
        $model = BspItem::model()->findByPk($slug_arr[0]);
        $priceCal = BspItemConditionHour::model()->findAll("item_id = " . $slug_arr[0]);
        $this->item = $model;
        $this->render("//offers/detail", array("model" => $model, "priceCal" => $priceCal));
    }

    /**
     * post offer to create post 
     * @param type $id
     * @param type $action
     */
    public function actionPost($id = 0, $action = "create") {
        $model = new BspItemFrontEnd();
        $user = ChangeUser::model()->findByPk(Yii::app()->user->id);

        if (isset($_POST['BspItemFrontEnd']) && isset($_POST['ChangeUser'])) {
            $model->attributes = $_POST['BspItemFrontEnd'];
            $user->attributes = $_POST['ChangeUser'];
            //set user avatar 

            $this->checkCilds($model);
            $isvalid = 1;
            if (!$model->validate()) {
                $isvalid = 0;
            }
            if (!$user->validate()) {
                $isvalid = 0;
            }
            if ($isvalid == 1) {
                if ($model->save()) {
                    $user->password = md5($model->password_new);
                    $user->save(false);
                    $item = BspItem::model()->findByPk($model->id);
                    $this->redirect($this->createUrl("/web/offers/detail",array("slug"=>$item->slug)));
                }
            }
        }
        $this->render("//offers/post", array("model" => $model, "user" => $user));
    }

    /**
     * Price calculation
     */
    public function actionCalculatePrice() {
        $model = new PriceCalculation();
        if (isset($_POST['PriceCalculation'])) {
            $model->attributes = $_POST['PriceCalculation'];
        }
        $item = BspItem::model()->findByPk($model->item_id);

        $periodmain = BspItem::getPeriod($item->per_price);

        $periods = $model->time_since($model->start_date . ' ' . $model->start_time, true, $model->end_date . ' ' . $model->end_time, $periodmain);
        $time = '';
        foreach ($periods as $label => $value) {
            $time .= $value . " " . $label . " ";
        }


        $prices = $item->offerPrices;
        $sum = 0;

        foreach ($prices as $price) {
            $period = BspItem::getPeriod($price->period);
            if (in_array($period, array_keys($periods))) {
                if ($periods[$period] > 0) {

                    if ($price->option == "abs") {
                        if ($price->start <= $periods[$period]) {
                            $sum += $price->start * $price->price;
                            $periods[$period] -= $price->start;
                        }
                    }
                    if ($price->option == "range") {
                        if ($price->end <= $periods[$period]) {
                            $range = ($price->end - $price->start) + 1;
                            $sum += $range * $price->price;
                            $periods[$period] -= $range;
                        } else {
                            $sum += $periods[$period] * $price->price;
                        }
                    }
                    if ($price->option == "extra" && $periods[$period] > 0) {
                        $sum += ($periods[$period]) * $price->price;
                    }
                }
            }
        }
        if (in_array($periodmain, array_keys($periods))) {
            if ($periods[$periodmain] > 0) {
                $price = $item->special_deal == 1 ? $item->discount_price : $item->price;
                $sum += $periods[$periodmain] * $price;
            }
        }

        echo CJSON::encode(array("period" => $time, "price" => $sum));
    }

    /**
     * render partial for ajax
     */
    public function actionAddpartial() {
        if (isset($_POST['partial']) && isset($_POST['ajax'])) {
            switch ($_POST['partial']) {
                case "_price_offer_day_row":
                    $model = new BspItemPriceOfferDay;
                    break;
                case "_price_offer_hour_row":
                    $model = new BspItemPriceOfferHour;
                    break;
                case "_price_offer_week_row":
                    $model = new BspItemPriceOfferWeek;
                    break;
                case "_price_offer_month_row":
                    $model = new BspItemPriceOfferMonth;
                    break;
                default:
                    break;
            }
            $this->renderPartial("//offers/price_offers/" . $_POST['partial'], array("model" => $model, "index" => $_POST['index']), false, true);
        }
    }

    /**
     * managing recrods
     * @param type $model
     * @return boolean
     */
    private function checkCilds($model) {
        if (isset($_POST['BspItemImage'])) {
            $model->setRelationRecords('image_items', is_array($_POST['BspItemImage']) ? $_POST['BspItemImage'] : array());
        }
        if (isset($_POST['BspItemVideo'])) {
            $model->setRelationRecords('item_video', is_array($_POST['BspItemVideo']) ? $_POST['BspItemVideo'] : array());
        }
        if (isset($_POST['BspItemSoundUrl'])) {
            $model->setRelationRecords('item_related_sounds', is_array($_POST['BspItemSoundUrl']) ? $_POST['BspItemSoundUrl'] : array());
        }
        /**
         * offer prices
         */
        if (isset($_POST['BspItemPriceOfferHour'])) {
            $model->setRelationRecords('item_price_offers_hour', is_array($_POST['BspItemPriceOfferHour']) ? $_POST['BspItemPriceOfferHour'] : array());
        }
        if (isset($_POST['BspItemPriceOfferDay'])) {
            $model->setRelationRecords('item_price_offers_day', is_array($_POST['BspItemPriceOfferDay']) ? $_POST['BspItemPriceOfferDay'] : array());
        }
        if (isset($_POST['BspItemPriceOfferMonth'])) {
            $model->setRelationRecords('item_price_offers_month', is_array($_POST['BspItemPriceOfferMonth']) ? $_POST['BspItemPriceOfferMonth'] : array());
        }
        if (isset($_POST['BspItemPriceOfferWeek'])) {
            $model->setRelationRecords('item_price_offers_week', is_array($_POST['BspItemPriceOfferWeek']) ? $_POST['BspItemPriceOfferWeek'] : array());
        }
        return true;
    }

}