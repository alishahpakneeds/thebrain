<?php

class BspBlogController extends Controller {

    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout = '//layouts/column2';

    /**
     * @return array action filters
     */
    public function filters() {
        return array(
            // 'accessControl', // perform access control for CRUD operations
            'rights',
        );
    }

    public function allowedActions() {
        return '@';
    }

    public function beforeAction($action) {
        parent::beforeAction($action);

        $operations = array('create', 'update', 'index', 'delete');
        parent::setPermissions($this->id, $operations);

        return true;
    }

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id) {
        $this->render('view', array(
            'model' => $this->loadModel($id),
        ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate() {
        $model = new BspBlog;

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['BspBlog'])) {
            $model->attributes = $_POST['BspBlog'];

            //making instance of the uploaded image 
            $img_file = DTUploadedFile::getInstance($model, 'img');
            $model->img = $img_file;
            if ($model->save()) {

                $upload_path = DTUploadedFile::creeatRecurSiveDirectories(array("blog", $model->id));
                if (!empty($img_file)) {
                    $img_file->saveAs($upload_path . $img_file->name);
                }

                $this->redirect(array('view', 'id' => $model->id));
            }
        }

        $this->render('create', array(
            'model' => $model,
        ));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id) {
        $model = $this->loadModel($id);
        $old_image = $model->img;

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['BspBlog'])) {
            $model->attributes = $_POST['BspBlog'];

            //making instance of the uploaded image 
            $img_file = DTUploadedFile::getInstance($model, 'img');
            if (!empty($img_file)) {
                $model->img = $img_file;
            } else {
                $model->img = $old_image;
            }
            if ($model->save()) {

                $upload_path = DTUploadedFile::creeatRecurSiveDirectories(array("blog", $model->id));
                if (!empty($img_file)) {
                    $img_file->saveAs($upload_path . $img_file->name);
                }

                $this->redirect(array('view', 'id' => $model->id));
            }
        }

        $this->render('update', array(
            'model' => $model,
        ));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id) {
        $this->loadModel($id)->delete();

        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if (!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
    }

    /**
     * Lists all models.
     */
    public function actionIndex() {
        $model = new BspBlog('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['BspBlog']))
            $model->attributes = $_GET['BspBlog'];

        $this->render('index', array(
            'model' => $model,
        ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return BspBlog the loaded model
     * @throws CHttpException
     */
    public function loadModel($id) {
        $model = BspBlog::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param BspBlog $model the model to be validated
     */
    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'bsp-blog-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

}
