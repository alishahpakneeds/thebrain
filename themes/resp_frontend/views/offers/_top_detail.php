<div class="row">
    <div class="row-holder">
        <div class="col-lg-2"></div>
        <div class="col-lg-8">
            <div class="puzzle-top text-center">
                <a href="javascript:void(0)">The Puzzzle I ALPHA</a>
                <div style="width:30px; float: right;cursor: pointer;" class="newfeed-close"><span class="k-icon k-i-close" id="newsfeed-close"></span></div>
            </div>
        </div>
        <div class="col-lg-2"></div>
    </div>
</div>
<?php
$background = !empty($model->background_image)?Yii::app()->baseUrl . "/uploads/BspItemImage/" . $model->id . "/" . $model->background_image:"";
?>
<div class="offer_item-top" style="width:100%;height:450px;background: url('<?php echo $background ?>')">
    <div class="container ">
        <div class="col-lg-12 offer_item">
            <div class="col-lg-8 offer_item-left">
                <div class="item_offer_name"><?php echo $model->name; ?></div>
                <div class="clear"></div>
                <div class="image-detail">
                    <div class="itemAvata col-lg-2">
                        <?php
                        $user = Users::model()->findByPk($model->user_id);
                        echo CHtml::image(Yii::app()->theme->baseUrl . "/images/noavatar.jpg", '', array("width" => "110"));
                        if (!empty($user->avatar)) {
                            $avatar = CHtml::image(Yii::app()->baseUrl . '/uploads/Users/' . $user->id . '/avatar/' . $user->avatar, '', array("width" => "110"));
                        } else {
                            $avatar = CHtml::image(Yii::app()->theme->baseUrl . '/images/noavatar.jpg', '', array("width" => "110"));
                        }
                        ?>

                        <div class="over-item-avata"></div>
                    </div>
                    <div class="col-lg-7">
                        <div class="contactLink">
                            <a id="contact-me"><?php echo Yii::t('detailOffer', 'Contact Me'); ?></a>
                        </div>
                        <div class="clear"></div>
                        <div id="offerDetail">
                            <?php
                            if (isset(Yii::app()->user)) {
                                echo CHtml::image(Yii::app()->theme->baseUrl . "/images/online.png", '', array("class" => "chk-online", "width" => "15", "height" => "15"));
                            } else {
                                echo CHtml::image(Yii::app()->theme->baseUrl . "/images/offline.png", '', array("class" => "chk-online", "width" => "15", "height" => "15"));
                            }
                            ?>
                            <div>
                                <?php
                                if (isset($model->user_rel)):
                                    ?>
                                    <?php echo Yii::t('detailOffer', 'from') ?> <?php echo $model->user_rel->first_name; ?>, 
                                    <?php echo substr($model->user_rel->second_name, 0, 1) . '.' ?> / <?php echo $model->user_rel->city ?> 
                                    <?php echo $model->user_rel->zipcode ?>
                                    <div class="clear clear-four"></div>
                                    <?php echo $model->user_rel->description ?> <?php echo Yii::t('detailOffer', 'Last seen') ?>: 
                                    <?php echo date("Y.M.d", strtotime($model->user_rel->lastActiveTime)) ?>
                                    <?php
                                endif;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="item_offer_price">
                    <?php
                    if ($model->special_deal == 1) {
                        echo Yii::t('detailOffer', 'Special Deal /');
                    }
                    else
                        echo Yii::t('detailOffer', 'Price / ');
                    if ($model->per_price == 1) {
                        echo Yii::t('detailOffer', 'Fix');
                    }
                    if ($model->per_price == 2) {
                        echo Yii::t('detailOffer', 'Per Hour');
                    }
                    if ($model->per_price == 3) {
                        echo Yii::t('detailOffer', 'Per Day');
                    }
                    ?>
                    <label>
                        <?php
                        $currency_symbol = "&euro;";
                        if (!empty($model->currency->symbol)) {
                            $currency_symbol = $model->currency->symbol;
                        }
                        if ($model->special_deal == 1) {
                            $price = ' 
                                    <span style="text-decoration: line-through">' . $model->price . '</span>
                                    <font size="2" class="item-discount"> ' . $currency_symbol . '</font>
                                    <span>' . $model->discount_price . '</span>
                                    
                                ';
                            echo $price;
                        } else {
                            echo $model->price;
                        }
                        ?>
                        <sup>
                            <?php
                            echo $currency_symbol;
                            ?>
                        </sup>
                    </label>
                </div>
                <div id='orderNowdiv'>
                    <a id="orderNow" href="javascript:#"><?php echo Yii::t('link', 'Order Now') ?></a>
                </div>
            </div>

        </div>
    </div>    

</div>