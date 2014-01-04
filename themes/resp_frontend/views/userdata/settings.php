<div class="tabs-container">

    <?php
    /**
     * tab bar
     */
    $this->renderPartial("//common/_tab_bar");
    ?>
    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'profile-form',
        //'enableClientValidation' => true,
        'htmlOptions' => array('enctype' => 'multipart/form-data', 'class' => 'form-horizontal'),
        'clientOptions' => array(
            'validateOnSubmit' => true,
        ),
    ));
    ?>
    <!-- Tab panes -->
    <div class="tab-content">
        <div class="tab-pane active" id="my_offers">
            <h1><?php echo Yii::t('user', 'Settings') ?></h1>
            <?php
            echo CHtml::image(Yii::app()->theme->baseUrl . "/images/tab_bg.png", '', array("class" => "line-blog"));
            ?>
            <div class="space-blog"></div>


            <?php
            if (Yii::app()->user->hasFlash('success')) {
                echo "<span class='alert alert-success'>" . Yii::app()->user->getFlash('success') . "</span>";
            }
            ?>
            <div class="form_part_container">


                <div class="col-md-6">
                    <div class="form-group">
                        <?php echo $form->labelEx($model, 'first_name', array('class' => 'control-label col-sm-4')); ?>
                        <div class="col-lg-7">
                            <?php echo $form->textField($model, 'first_name', array('class' => 'form-control')); ?> 
                            <?php echo $form->error($model, 'first_name', array("class" => 'alert alert-error')); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php echo $form->labelEx($model, 'second_name', array('class' => 'control-label col-sm-4')); ?>
                        <div class="col-lg-7">
                            <?php echo $form->textField($model, 'second_name', array('class' => 'form-control')); ?> 
                            <?php echo $form->error($model, 'second_name', array("class" => 'alert alert-error')); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php echo $form->labelEx($model, 'address', array('class' => 'control-label col-sm-4')); ?>
                        <div class="col-lg-7">
                            <?php echo $form->textField($model, 'address', array('class' => 'form-control')); ?> 
                            <?php echo $form->error($model, 'address', array("class" => 'alert alert-error')); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php echo $form->labelEx($model, 'city', array('class' => 'control-label col-sm-4')); ?>
                        <div class="col-lg-7">
                            <?php echo $form->textField($model, 'city', array('class' => 'form-control')); ?> 
                            <?php echo $form->error($model, 'city', array("class" => 'alert alert-error')); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php echo $form->labelEx($model, 'country', array('class' => 'control-label col-sm-4')); ?>
                        <div class="col-lg-7">
                            <?php echo $form->textField($model, 'country', array('class' => 'form-control')); ?> 
                            <?php echo $form->error($model, 'country', array("class" => 'alert alert-error')); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <?php echo $form->labelEx($model, 'zipcode', array('class' => 'control-label col-sm-4')); ?>
                        <div class="col-lg-6">
                            <?php echo $form->textField($model, 'zipcode', array('class' => 'form-control')); ?> 
                            <?php echo $form->error($model, 'zipcode', array("class" => 'alert alert-error')); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php echo $form->labelEx($model, 'phone', array('class' => 'control-label col-sm-4')); ?>
                        <div class="col-lg-6">
                            <?php echo $form->textField($model, 'phone', array('class' => 'form-control')); ?> 
                            <?php echo $form->error($model, 'phone', array("class" => 'alert alert-error')); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php echo $form->labelEx($model, 'birthday', array('class' => 'control-label col-sm-4')); ?>
                        <div class="col-lg-6">
                            <?php
                            $this->widget('ItstJUIDatePicker', array(
                                'model' => $model,
                                'attribute' => 'birthday',
                                'model_attribute' => 'birthday',
                                'options' => array('showAnim' => 'fold',
                                    'dateFormat' => Yii::app()->params['dateformat'],
                                    'changeYear' => true,
                                ),
                                'htmlOptions' => array('class' => 'form-control')
                            ));
                            ?>
                            <?php echo $form->error($model, 'birthday', array("class" => 'alert alert-error')); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php echo $form->labelEx($model, 'paypal_mail', array('class' => 'control-label col-sm-4')); ?>
                        <div class="col-lg-6">
                            <?php echo $form->textField($model, 'paypal_mail', array('class' => 'form-control')); ?> 
                            <?php echo $form->error($model, 'paypal_mail', array("class" => 'alert alert-error')); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php echo $form->labelEx($model, 'gender', array('class' => 'control-label col-sm-4')); ?>
                        <div class="col-lg-6">
                            <?php echo $form->dropDownList($model, 'gender', array("1" => "Male", "2" => "Female"), array('class' => 'form-control')); ?> 
                            <?php echo $form->error($model, 'gender', array("class" => 'alert alert-error')); ?>
                        </div>
                    </div>
                </div>

            </div>
            <div class="form_2nd_part_container">
                <div id="storefront_bigimagecontent">
                    <?php
                    if (!empty($model->background)) {
                        $background = Yii::app()->baseUrl . "/uploads/Users/" . $model->id . "/background/" . $model->background;
                        echo CHtml::image($background, '', array("class" => "background_setting"));
                    } else {
                        echo CHtml::image("", '', array("class" => "background_setting"));
                    }
                    ?>
                </div>
                <div class="background_select">
                    <?php
                    $uploadTemp = new UploadTemp();
                    echo zHtml::activeFileField($uploadTemp, '[' . 1 . ']upload_temp_image');
                    ?>
                    <?php echo $form->hiddenField($model, 'background', array('class' => 'form-control')); ?>
                </div>
                <div class="avatar_select">
                    <?php
                    echo zHtml::activeFileField($uploadTemp, "upload_temp_image");
                    ?>
                    <?php echo $form->hiddenField($model, 'avatar', array('class' => 'form-control')); ?>
                </div>



            </div>
        </div>
    </div>
    <?php $this->endWidget(); ?>
</div>

<script>
    jQuery(function() {

        jQuery("#UploadTemp_1_upload_temp_image").kendoUpload({
            async: {
                saveUrl: "<?php
    echo $this->createUrl("/site/uploadTemp", array("index" => 1, "model" => get_class($model), "attribute" => "Users_background"));
    ?>",
                autoUpload: true
            },
            localization: {
                "select": "Select Your Store Front Image"
            },
            cancel: function(e) {

            },
            complete: function(e) {

            },
            error: function(e) {

            },
            progress: function(e) {

            },
            remove: function(e) {

            },
            select: function(e) {
                jQuery("#loading").show();
                jQuery(".avatar_select .k-upload-files.k-reset").show();
            },
            success: function(e) {

                path = "<?php echo Yii::app()->baseUrl . "/uploads/temp/" . Yii::app()->user->id . "/Users/Users_background/" ?>" + e.response.file;
                jQuery("#loading").hide();
                jQuery("#Users_background").val(e.response.file);
                jQuery(".background_setting").attr("src", path);
                jQuery(".background_select .k-upload-files.k-reset").hide();
            },
            upload: function(e) {

            },
        });
        jQuery("#UploadTemp_upload_temp_image").kendoUpload({
            async: {
                saveUrl: "<?php
    echo $this->createUrl("/site/uploadTemp", array("index" => 1, "model" => get_class($model), "attribute" => "Users_avatar"));
    ?>",
                autoUpload: true
            },
            localization: {
                "select": "Select Your Avatar Image"
            },
            cancel: function(e) {

            },
            complete: function(e) {

            },
            error: function(e) {

            },
            progress: function(e) {

            },
            remove: function(e) {

            },
            select: function(e) {
                jQuery("#loading").show();
                jQuery(".avatar_select .k-upload-files.k-reset").show();
            },
            success: function(e) {

                path = "<?php echo Yii::app()->baseUrl . "/uploads/temp/" . Yii::app()->user->id . "/Users/Users_avatar/" ?>" + e.response.file;
                jQuery("#loading").hide();
                jQuery("#Users_background").val(e.response.file);
                jQuery(".background_setting").attr("src", path);
                jQuery(".avatar_select .k-upload-files.k-reset").hide();
            },
            upload: function(e) {

            },
        });
    })
</script>