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
                    'changeStatus',
                    'deleteOffer',
                    'getChildrenCategories',
                    'sentMessage',
                    'deleteoffertype',
                    'orderOffer',
                    'notificationdetail',
                    'notifications',
                    'setPaymentStatus',
                    'payPallPayment',
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
        $criteria->addCondition("iStatus = 1");
        $dataProvider = new CActiveDataProvider('BspItem', array(
            'criteria' => $criteria,
            'pagination' => array('pageSize' => 15)
        ));
        if (isset($_POST['ajax']) && isset($_POST['update_element_id'])) {
            if ($_POST['update_element_id'] == "grid-view-div") {
                $this->renderPartial("//offers/_grid_offers", array(
                    "cat_arr" => $cat_arr,
                    "dataProvider" => $dataProvider,
                    'criteria' => $criteria,
                ));
            } else {

                $this->renderPartial("//user/_tab_items", array(
                    "cat_arr" => $cat_arr,
                    "dataProvider" => $dataProvider,
                    'criteria' => $criteria,
                    "items" => $dataProvider->getData(),
                ));
                //thumb-view-div
            }
        } else {
            $this->render("//offers/category", array(
                "cat_arr" => $cat_arr,
                "dataProvider" => $dataProvider,
                'criteria' => $criteria,
            ));
        }
    }

    /**
     * search page
     */
    public function actionSearch() {
        $model = new OfferSearch;
        $criteria = new CDbCriteria();
        $dataItem = array();
        if (isset($_GET['OfferSearch'])) {
            $model->attributes = $_GET['OfferSearch'];
            if (!empty($model->keyword)) {
                $criteria->compare("name", $model->keyword, true, "OR");
                $criteria->compare("name", $model->keyword, true, "OR");
                $criteria->compare("t.id", $model->keyword, true, "OR");


                $criteria->compare("offer_number", $model->keyword, true, "OR");
                $criteria->compare("description", $model->keyword, true, "OR");
                $criteria->compare("seo_keywords", $model->keyword, true, "OR");
                $criteria->compare("seo_title", $model->keyword, true, "OR");
            }
            $order_by = array();
//            $criteria->compare("lat", $model->lat);
//            $criteria->compare("lng", $model->lng);
            //new attribute
            //$criteria->compare("special_deal", $model->special_deal);

            if ($model->special_deal == 1) {
                $criteria->addCondition("t.discount_price IS NOT NULL AND t.discount_price !=''");
                $order_by[] = "t.discount_price DESC";
            }
            $criteria->addCondition("iStatus = 1");
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
                $with ['item_rating'] = array(
                    'joinType' => 'LEFT JOIN',
                        //'order' => 'item_rating.rating DESC',
                );
                $order_by[] = "item_rating.rating DESC";
            }

            if ($model->nearFirst == 1) {
                $criteria->order = "t.id ASC";
            }

            if (!empty($with)) {
                $criteria->with = $with;
            }

            if ($model->lowPrice == 1) {
                $order_by[] = "t.price ASC";
            }
            if ($model->highPrice == 1) {

                $order_by[] = "t.price DESC";
            }

            if ($model->lat != "" && $model->lng != "" && $model->distance != "") {


                //$model->lat = 43;
                //$model->lng = 64;
                $condition = ' distance  <=  ' . ($model->distance) * 1000;
                if ($model->distance == "all") {
                    $condition = "";
                }
                $select = '((6372.797 * (2 *
        ATAN2(
            SQRT(
                SIN((' . ($model->lat * 1) . ' * (PI()/180)-lat*(PI()/180))/2) *
                SIN((' . ($model->lat * 1) . ' * (PI()/180)-lat*(PI()/180))/2) +
                COS(lat * (PI()/180)) *
                COS(' . ($model->lat * 1) . ' * (PI()/180)) *
                SIN((' . ($model->lng * 1) . ' * (PI()/180)-lng*(PI()/180))/2) *
                SIN((' . ($model->lng * 1) . ' * (PI()/180)-lng*(PI()/180))/2)
                ),
            SQRT(1-(
                SIN((' . ($model->lat * 1) . ' * (PI()/180)-lat*(PI()/180))/2) *
                SIN((' . ($model->lat * 1) . ' * (PI()/180)-lat*(PI()/180))/2) +
                COS(lat * (PI()/180)) *
                COS(' . ($model->lat * 1) . ' * (PI()/180)) *
                SIN((' . ($model->lng * 1) . ' * (PI()/180)-lng*(PI()/180))/2) *
                SIN((' . ($model->lng * 1) . ' * (PI()/180)-lng*(PI()/180))/2)
            ))
        )
        ))) as distance ';

                $criteria->select = '*,' . $select;
                $criteria->having = $condition;
                $order_by [] = ' distance ASC';
                $criteria->addCondition("lat IS NOT NULL AND lng IS NOT NULL ");
                //$sql = "SELECT * FROM bsp_item WHERE ".$this->mysqlHaversine($model->lat,$model->lng,$model->distance);
                //$dataItem = Yii::app()->db->createCommand($sql)->queryAll();
                //$items = CHtml::listData($dataItem, 'id', 'id');
                //
                //  CVarDumper::dump($items, 10, true);
                //echo $sql'
//                $users = $model->getLantLongUser();
//                $search_users = array();
//                foreach ($users as $user) {
//                    if ($model->getDistantByLocation($model->lat, $model->lng, $user->lat, $user->lng, $model->distance) > 0) {
//                        $search_users[$user->id] = $user->id;
//                    }
//                }
//                if (!empty($dataItem)) {
//                    //$criteria->addInCondition("user_id", $search_users);
//                    $criteria->addInCondition("t.id", $items);
//                }
            }
        }
        if (!empty($order_by)) {
            $order_by = implode(",", $order_by);
            $criteria->order = $order_by;
        }
        /**
         * group id setting
         */
        if (!empty($_GET['grp_id'])) {
            $criteria->addCondition("group_id = " . $_GET['grp_id']);
        }
//        CVarDumper::dump($model->attributes,10,true);
//        CVarDumper::dump($_POST['OfferSearch'],10,true);

        $dataProvider = new CActiveDataProvider('BspItem', array(
            'criteria' => $criteria,
            'pagination' => array('pageSize' => 1000)
        ));

        if (isset($_GET['ajax'])) {
            $this->renderPartial("//offers/_search_result", array(
                "cat_arr" => array("0" => "", 1 => ""),
                "dataProvider" => $dataProvider,
                "radius" => $model->distance,
                "dataItem" => $dataItem,
            ));
        } else {
            $this->render("//offers/search", array(
                "cat_arr" => array("0" => "", 1 => ""),
                "dataProvider" => $dataProvider,
                "radius" => $model->distance,
                "dataItem" => $dataItem,
            ));
        }
    }

    /**
     * 
     * @param type $id
     */
    public function actionChangeStatus($id) {
        $offer = BspItem::model()->findByPk($id);

        if ($offer->iStatus == 0) {
            BspItem::model()->updateByPk($id, array("iStatus" => 1));
            Yii::app()->user->setFlash("offer-status", 'Offer Status has been Active Now');
        } else if ($offer->iStatus == 1) {
            BspItem::model()->updateByPk($id, array("iStatus" => 0));
            Yii::app()->user->setFlash("offer-status", 'Offer Status has been In-Active Now');
        }

        $this->redirect($this->createUrl("/web/userdata/myoffers"));
    }

    /**
     * 
     * @param type $id
     */
    public function actionDeleteOffer($id) {

        BspItem::model()->deleteByPk($id);
        Yii::app()->user->setFlash("offer-status", 'You have deleted offer');
        $this->redirect($this->createUrl("/web/userdata/myoffers"));
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
        $model->saveViewerForLog();
        $this->render("//offers/detail", array("model" => $model, "priceCal" => $priceCal));
    }

    /**
     * post offer to create post 
     * @param type $id
     * @param type $action
     */
    public function actionPost($slug = "", $action = "create") {
        $model = new BspItemFrontEnd();
        $user = ChangeUser::model()->findByPk(Yii::app()->user->id);

        if ($slug != "") {
            $slug_arr = explode("-", $slug);
            $id = $slug_arr[0];
            $model = BspItemFrontEnd::model()->findByPk($id, "user_id =" . Yii::app()->user->id);
            $model->saveViewerForLog();
            //setting one if the present its images;
            if ($model->num_image_items > 0) {
                $model->_offer_images = 1;
            }
            if (empty($model)) {
                throw new CHttpException(404, 'The specified post cannot be found.');
            }
        }

        if (isset($_POST['BspItemFrontEnd']) && isset($_POST['ChangeUser'])) {
            $model->attributes = $_POST['BspItemFrontEnd'];
            $user->attributes = $_POST['ChangeUser'];
            //set user avatar 

            $this->checkCilds($model);
            $isvalid = 1;

            if ($this->setOfferImage($model)) {
                
            }

            if ($this->setOfferVideos($model)) {
                
            }

            if (!$model->validate()) {
                $isvalid = 0;
            }
            if (!$user->validate()) {
                $isvalid = 0;
            }

            if ($isvalid == 1) {
                if ($model->save()) {
                    //incase of !empty password then the login 
                    if (!empty($user->password_new)) {

                        $user->password = md5($user->password_new);
                    } else {
                        unset($user->password);
                    }

                    $user->save(false);
                    foreach ($model->image_items as $modelImg) {
                        $modelImg->item_id = $model->id;

                        $modelImg->save();
                        //CVarDumper::dump($modelImg->getErrors(), 10, true);
                        //CVarDumper::dump($modelImg->attributes, 10, true);
                    }

                    foreach ($model->item_video_front as $modelVid) {
                        $modelVid->item_id = $model->id;

                        $modelVid->save();
                    }

                    $item = BspItem::model()->findByPk($model->id);
                    $this->redirect($this->createUrl("/web/offers/detail", array("slug" => $item->slug)));
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
     *  set offer images
     * @param type $model
     */
    public function setOfferImage($model) {
        $is_valid = 0;

        if (isset($_POST['BspItemImage'])) {
            $bspItem_imag = array();

            foreach ($_POST['BspItemImage'] as $key => $bspItemImg) {

                if (!empty($bspItemImg['id'])) {
                    $modelItemImg = BspItemImage::model()->findByPk($bspItemImg['id']);
                    if (empty($modelItemImg->id)) {
                        $modelItemImg = new BspItemImage;
                    }
                } else {
                    $modelItemImg = new BspItemImage;
                }
                $modelItemImg->attributes = $bspItemImg;

                if ($modelItemImg->validate()) {
                    $is_valid = 1;
                }
                $bspItem_imag [] = $modelItemImg;
            }
            $model->image_items = $bspItem_imag;
            //in post also setttle this thing
            $model->_offer_images = 1;
        }

        return $is_valid;
    }

    /**
     * set offer videos
     */
    public function setOfferVideos($model) {
        $is_valid = 0;
        if (!empty($_POST['BspItemVideoFrontEnd'])) {
            $bspItem_vid = array();

            foreach ($_POST['BspItemVideoFrontEnd'] as $bspItemVideo) {
                if (!empty($bspItemVideo['id'])) {

                    $modelItemVid = BspItemVideoFrontEnd::model()->findByPk($bspItemVideo['id']);
                    if (empty($modelItemVid)) {
                        $modelItemVid = new BspItemVideoFrontEnd;
                    }
                } else {
                    $modelItemVid = new BspItemVideoFrontEnd;
                }
                $modelItemVid->attributes = $bspItemVideo;

                if ($modelItemVid->validate()) {
                    $is_valid = 1;
                }
                $bspItem_vid [] = $modelItemVid;
            }

            $model->item_video_front = $bspItem_vid;
        }

        return $is_valid;
    }

    /**
     * managing recrods
     * @param type $model
     * @return boolean
     */
    private function checkCilds($model) {
        /**
         * Bsp Item Image
         */
//        if (!empty($_POST['BspItemVideoFrontEnd'])) {
//            $model->setRelationRecords('item_video_front', is_array($_POST['BspItemVideoFrontEnd']) ? $_POST['BspItemVideoFrontEnd'] : array());
//        }
        if (isset($_POST['BspItemSoundUrl'])) {
            $model->setRelationRecords('item_related_sounds', is_array($_POST['BspItemSoundUrl']) ? $_POST['BspItemSoundUrl'] : array());
        }
        /**
         * offer prices
         */
        if ($model->per_price == 2) {
            $this->setOfferHour($model);
        } else if ($model->per_price == 3) {
            $this->setOfferDay($model);
        } else if ($model->per_price == 4) {
            $this->setOfferWeek($model);
        } else if ($model->per_price == 5) {
            $this->setOfferMonth($model);
        }
        return true;
    }

    /**
     * set price hour
     */
    public function setOfferHour($model) {
        if (isset($_POST['BspItemPriceOfferHour'])) {
            $model->setRelationRecords('item_price_offers_hour', is_array($_POST['BspItemPriceOfferHour']) ? $_POST['BspItemPriceOfferHour'] : array());
        }
    }

    /**
     * set price DAy
     */
    public function setOfferDay($model) {
        if (isset($_POST['BspItemPriceOfferHour'])) {
            $model->setRelationRecords('item_price_offers_hour', is_array($_POST['BspItemPriceOfferHour']) ? $_POST['BspItemPriceOfferHour'] : array());
        }
        if (isset($_POST['BspItemPriceOfferDay'])) {
            $model->setRelationRecords('item_price_offers_day', is_array($_POST['BspItemPriceOfferDay']) ? $_POST['BspItemPriceOfferDay'] : array());
        }
    }

    /**
     * set price Week
     */
    public function setOfferWeek($model) {
        if (isset($_POST['BspItemPriceOfferHour'])) {
            $model->setRelationRecords('item_price_offers_hour', is_array($_POST['BspItemPriceOfferHour']) ? $_POST['BspItemPriceOfferHour'] : array());
        }
        if (isset($_POST['BspItemPriceOfferDay'])) {
            $model->setRelationRecords('item_price_offers_day', is_array($_POST['BspItemPriceOfferDay']) ? $_POST['BspItemPriceOfferDay'] : array());
        }
        if (isset($_POST['BspItemPriceOfferWeek'])) {
            $model->setRelationRecords('item_price_offers_week', is_array($_POST['BspItemPriceOfferWeek']) ? $_POST['BspItemPriceOfferWeek'] : array());
        }
    }

    /**
     * set price Month
     */
    public function setOfferMonth($model) {
        if (isset($_POST['BspItemPriceOfferHour'])) {
            $model->setRelationRecords('item_price_offers_hour', is_array($_POST['BspItemPriceOfferHour']) ? $_POST['BspItemPriceOfferHour'] : array());
        }
        if (isset($_POST['BspItemPriceOfferDay'])) {
            $model->setRelationRecords('item_price_offers_day', is_array($_POST['BspItemPriceOfferDay']) ? $_POST['BspItemPriceOfferDay'] : array());
        }
        if (isset($_POST['BspItemPriceOfferWeek'])) {
            $model->setRelationRecords('item_price_offers_week', is_array($_POST['BspItemPriceOfferWeek']) ? $_POST['BspItemPriceOfferWeek'] : array());
        }
        if (isset($_POST['BspItemPriceOfferMonth'])) {
            $model->setRelationRecords('item_price_offers_month', is_array($_POST['BspItemPriceOfferMonth']) ? $_POST['BspItemPriceOfferMonth'] : array());
        }
    }

    /**
     * get children category
     */
    public function actionGetChildrenCategories() {
        $data = BspCategory::model()->getChildrenCategories($_REQUEST['id']);
        echo CJSON::encode($data);
    }

    /**
     * sent Message
     */
    public function actionSentMessage() {
        $model = new BspMessage;
        $recieve_user = "";
        if (isset($_POST['BspMessage'])) {
            $model->attributes = $_POST['BspMessage'];
            $model->user_send = Yii::app()->user->id;
            $recieve_user = Users::model()->findByPk($model->user_receive);
            $attachment = $model->attachment;
            if ($model->validate()) {
                if ($model->save()) {
                    if (!empty($attachment)) {
                        $upload_path = DTUploadedFile::creeatRecurSiveDirectories(array("message", $model->Id));
                        $source = $upload_path = DTUploadedFile::getFolderPath(array("temp", Yii::app()->user->id, get_class($model), get_class($model) . "_attachment"));
                        if (is_file($source . $model->attachment)) {
                            copy($source . $model->attachment, $upload_path . $model->attachment);
                            BspMessage::model()->updateByPk($model->Id, array("sFile" => $model->attachment));
                            $email['attachment'] = $source . $model->attachment;
                            $email['type'] = $this->get_mime($source . $model->attachment);
                            $email['name'] = $model->attachment;
                        }
                    }

                    $email['From'] = Yii::app()->user->user->user_email;
                    $email['FromName'] = Yii::app()->user->user->username;
                    $email['To'] = $recieve_user->user_email = "itsgeniusstar@gmail.com";
                    $email['Subject'] = $model->subject;
                    $email['Body'] = $model->detail;
                    $email['Body'] = $this->renderPartial('//common/_email_template', array('email' => $email), true, false);


                    $this->sendEmail2($email);

                    //set flash
                    Yii::app()->user->setFlash("success", "Your Message has been sent");
                }
            }
        }

        $this->renderPartial("//offers/_sent_message", array("model" => $model, "recieve_user" => $recieve_user));
    }

    /**
     * 
     * @param type $file
     * @return boolean
     */

    /**
     * Tries to get mime data of the file. 
     * @return {String} mime-type of the given file 
     * @param $filename String 
     */
    function get_mime($filename) {
        preg_match("/\.(.*?)$/", $filename, $m);    # Get File extension for a better match 
        switch (strtolower($m[1])) {
            case "js": return "application/javascript";
            case "json": return "application/json";
            case "jpg": case "jpeg": case "jpe": return "image/jpg";
            case "png": case "gif": case "bmp": return "image/" . strtolower($m[1]);
            case "css": return "text/css";
            case "xml": return "application/xml";
            case "html": case "htm": case "php": return "text/html";
            default:
                if (function_exists("mime_content_type")) { # if mime_content_type exists use it. 
                    $m = mime_content_type($filename);
                } else if (function_exists("")) {    # if Pecl installed use it 
                    $finfo = finfo_open(FILEINFO_MIME);
                    $m = finfo_file($finfo, $filename);
                    finfo_close($finfo);
                } else {    # if nothing left try shell 
                    if (strstr($_SERVER[HTTP_USER_AGENT], "Windows")) { # Nothing to do on windows 
                        return ""; # Blank mime display most files correctly especially images. 
                    }
                    if (strstr($_SERVER[HTTP_USER_AGENT], "Macintosh")) { # Correct output on macs 
                        $m = trim(exec('file -b --mime ' . escapeshellarg($filename)));
                    } else {    # Regular unix systems 
                        $m = trim(exec('file -bi ' . escapeshellarg($filename)));
                    }
                }
                $m = split(";", $m);
                return trim($m[0]);
        }
    }

    /**
     * delete offer type
     * like saved or recent
     */
    public function actionDeleteoffertype() {
        if (isset($_POST['offer_id'])) {
            if ($_POST['offer_type'] == "recent") {
                $criteria = new CDbCriteria();
                $criteria->addCondition("item_id =" . $_POST['offer_id']);
                $items = BspItemLog::model()->findAll($criteria);
                foreach ($items as $item) {
                    BspItemLog::model()->deleteByPk($item->id);
                }
            } else if ($_POST['offer_type'] == "saved") {
                $criteria = new CDbCriteria();
                $criteria->addCondition("item_id =" . $_POST['offer_id']);
                $criteria->addCondition("create_user_id = " . Yii::app()->user->id);
                $items = BspFarvorite::model()->findAll($criteria);
                foreach ($items as $item) {
                    BspFarvorite::model()->deleteByPk($item->id);
                }
            }
        }
    }

    /**
     * 
     */
    public function actionOrderOffer($id) {
        $offer = BspItem::model()->findByPk($id);
        $current_user = Yii::app()->user->user;
        $old = PaymentPaypallAdaptive::model()->saveInitialPaymentOrder($offer->user_rel, $offer);
        if ($old) {
            $this->sendNotificattion($offer->user_rel);
            if ($current_user->paypal_mail == "" || $offer->user_rel->paypal_mail == "") {
                if ($current_user->paypal_mail == "") {
                    echo CJSON::encode(array("ack" => "Warning", "warning" => "Kindly Update Your paypall Email"));
                } else if ($offer->user_rel->paypal_mail == "") {
                    echo CJSON::encode(array("ack" => "Warning", "warning" => "Offer Owner user has configured paypall Email <br/>but we have sent him your notification"));
                }
            } else {
                echo CJSON::encode(array("ack" => "Success", "success" => "Your notification has been sent to seller"));
            }
        } else {
            echo CJSON::encode(array("ack" => "Warning", "warning" => "You have already placed this offer"));
        }
    }

    /**
     * send notification to user 
     */
    public function sendNotificattion($owner) {

        $email['From'] = Yii::app()->user->user->user_email;
        $userFullName = Yii::app()->user->user->first_name . " " . Yii::app()->user->user->second_name . " ";
        $email['FromName'] = $userFullName . Yii::app()->name;
        $email['To'] = $owner->user_email;
        $email['Subject'] = "Inivitation to purchase Offer";
        $email['Body'] = $userFullName . " wants to buy your offer ! ";
        if ($owner->paypal_mail == "") {
            $email['Body'].= " you didn't configure your paypall email kindly configure <br/> to recieve this offer ";
            $email['Body'].= " <br/> click on following link after Login <br/>";
            $email['Body'].= CHtml::link($this->createAbsoluteUrl("/web/user/profile"), $this->createAbsoluteUrl("/web/user/profile"));
        }
        $email['Body'] = $this->renderPartial('//common/_email_template', array('email' => $email), true, false);

        $this->sendEmail2($email);
    }

    /**
     *  notification detail
     */
    public function actionNotificationdetail($id) {
        $model = BspNotify::model()->findByPk($id);
        //updating view status
        $model->updateByPk($id, array("isview" => 1));
        $this->render("//offers/notification_detail", array("model" => $model));
    }

    /**
     * all notifications
     */
    public function actionNotifications() {
        
    }

    /**
     * 
     * @param type $id
     * @param type $status
     */
    public function actionSetPaymentStatus($id, $status) {
        $model = BspNotify::model()->findByPk($id);

        PaymentPaypallAdaptive::model()->updateByPk($model->payment_adaptive->id, array("seller_status" => $status));
        $paymentAdaptive = PaymentPaypallAdaptive::model()->findByPk($model->payment_adaptive->id);
        $paymentAdaptive->saveHistory();
        Yii::app()->user->setFlash("success", "Your Notification sent to your customer");


        //send notifcationi

        $email['From'] = Yii::app()->user->user->user_email;
        $userFullName = Yii::app()->user->user->first_name . " " . Yii::app()->user->user->second_name . " ";
        $email['FromName'] = $userFullName . Yii::app()->name;
        $email['To'] = $model->payment_adaptive->buyer->user_email;
        $email['Subject'] = "your offer buy invitation has been " . ucfirst($status);
        $email['Body'] = $userFullName . " has  " . ucfirst($status) . " Your invitation";
        //setting notification
        $paymentAdaptive->generateNotification($model->payment_adaptive->buyer->id, $paymentAdaptive->id, "buyer", $email['Subject']);
        if ($status == "rejected") {
            $email['Body'].= "<br/> you can try again !";
        } else if ($status == "confirmed") {
            $email['Body'].= "<br/> at both of your completing this offer status offer you will be purchased this offer";
        }

        $email['Body'] = $this->renderPartial('//common/_email_template', array('email' => $email), true, false);

        $this->sendEmail2($email);

        $this->redirect($this->createUrl("/web/offers/notificationdetail", array("id" => $id)));
    }

    /**
     * 
     * @param type $id
     * @param type $status
     * when buyer will set confirm
     * 
     */
    public function actionPayPallPayment($id, $status) {
        $model = BspNotify::model()->findByPk($id);

        PaymentPaypallAdaptive::model()->updateByPk($model->payment_adaptive->id, array("buyer_status" => $status));
        $paymentAdaptive = PaymentPaypallAdaptive::model()->findByPk($model->payment_adaptive->id);
        $paymentAdaptive->saveHistory();
        Yii::app()->user->setFlash("success", "Your Notification sent to your customer");


        //send notifcationi

        $email['From'] = Yii::app()->user->user->user_email;
        $userFullName = Yii::app()->user->user->first_name . " " . Yii::app()->user->user->second_name . " ";
        $email['FromName'] = $userFullName . Yii::app()->name;
        $email['To'] = $model->payment_adaptive->seller->user_email;

        //setting notification
        
        if ($status == "paying") {
            $paymentAdaptive->payToPuzzle($paymentAdaptive);
        } else if ($status == "cancelled") {
            //setting notification
                        $email['Subject'] = "buyer (" . $userFullName . ") has  " . ucfirst($status) . " the offer to buy ";
            $paymentAdaptive->generateNotification($model->payment_adaptive->seller->id, $paymentAdaptive->id, "seller", $email['Subject']);

            $email['Body'] = $userFullName . " has  " . ucfirst($status) . " the offer to buy ";
            $email['Body'].= "<br/> May be some issue to whether he has'nt too much amount in his paypall account";
            $email['Body'] = $this->renderPartial('//common/_email_template', array('email' => $email), true, false);

            $this->sendEmail2($email);
        }
         else if ($status == "completed") {
            //setting notification
              $email['Subject'] = "buyer (" . $userFullName . ") has  " . ucfirst($status) . " the offer and sent to you money ";
            $paymentAdaptive->generateNotification($model->payment_adaptive->seller->id, $paymentAdaptive->id, "seller", $email['Subject']);
           
            $email['Body'] = $userFullName . " has  " . ucfirst($status) . " the offer and sent to you money ";
            $email['Body'].= "<br/> after 48 hours money will be transfered to you";
            $email['Body'] = $this->renderPartial('//common/_email_template', array('email' => $email), true, false);

            $this->sendEmail2($email);
        }



        $this->redirect($this->createUrl("/web/offers/notificationdetail", array("id" => $id)));
    }

    /**
     * 
     * @param type $lat
     * @param type $lon
     * @param type $distance
     * @return string
     */
    function mysqlHaversine($lat = 0, $lon = 0, $distance = 0) {
        if ($distance > 0) {
            return ('
        ((6372.797 * (2 *
        ATAN2(
            SQRT(
                SIN((' . ($lat * 1) . ' * (PI()/180)-lat*(PI()/180))/2) *
                SIN((' . ($lat * 1) . ' * (PI()/180)-lat*(PI()/180))/2) +
                COS(lat * (PI()/180)) *
                COS(' . ($lat * 1) . ' * (PI()/180)) *
                SIN((' . ($lon * 1) . ' * (PI()/180)-lng*(PI()/180))/2) *
                SIN((' . ($lon * 1) . ' * (PI()/180)-lng*(PI()/180))/2)
                ),
            SQRT(1-(
                SIN((' . ($lat * 1) . ' * (PI()/180)-lat*(PI()/180))/2) *
                SIN((' . ($lat * 1) . ' * (PI()/180)-lat*(PI()/180))/2) +
                COS(lat * (PI()/180)) *
                COS(' . ($lat * 1) . ' * (PI()/180)) *
                SIN((' . ($lon * 1) . ' * (PI()/180)-lng*(PI()/180))/2) *
                SIN((' . ($lon * 1) . ' * (PI()/180)-lng*(PI()/180))/2)
            ))
        )
        )) <= ' . ($distance / 1000) . ')');
        }

        return '';
    }

}