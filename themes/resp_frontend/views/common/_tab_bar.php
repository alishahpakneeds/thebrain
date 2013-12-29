<?php
$countper = 10;

if (Yii::app()->user->user->first_name == '')
    $countper--;
if (Yii::app()->user->user->second_name == '')
    $countper--;
if (Yii::app()->user->user->phone == '')
    $countper--;
if (Yii::app()->user->user->birthday == '0000-00-00')
    $countper--;
if (Yii::app()->user->user->description == '')
    $countper--;
if (Yii::app()->user->user->city == '')
    $countper--;
if (Yii::app()->user->user->country == '')
    $countper--;
if (Yii::app()->user->user->zipcode == '')
    $countper--;
if (Yii::app()->user->user->avatar == '' || Yii::app()->user->user->avatar == 'no_image')
    $countper--;
if (Yii::app()->user->user->background == '' || Yii::app()->user->user->background == 'no_image')
    $countper--;
?>
<ul class="nav nav-tabs">
    <li class="<?php echo $this->action->id == "messages" ? "active" : ""; ?>">
        <a onclick="window.location = jQuery(this).attr('href')" href="<?php echo $this->createUrl("/web/user/messages"); ?>" >My Mails <span><?php echo Yii::app()->user->user->statmessagesRecv; ?></span>
        </a>
    </li>
    <li class="<?php echo $this->action->id == "myoffers" ? "active" : ""; ?>">
        <a  href="<?php echo $this->createUrl("/web/userdata/myoffers"); ?>" >My Offers <span> <?php echo Yii::app()->user->user->numitems; ?> </span></a>
    </li>
    <li class="<?php echo $this->action->id == "myorders" ? "active" : ""; ?>">
        <a href="<?php echo $this->createUrl("/web/userdata/myorders"); ?>" >My Orders <span><?php echo Yii::app()->user->user->numseller_orders + Yii::app()->user->user->numbuyer_orders; ?></span></a>
    </li>
    <li class="<?php echo $this->action->id == "settings" ? "active" : ""; ?>">
        <a href="<?php echo $this->createUrl("/web/userdata/settings"); ?>" >Settings<span><?php echo $countper * 10; ?>%</a></span>
    </li>
    <li class="<?php echo $this->action->id == "payment" ? "active" : ""; ?>">
        <a href="<?php echo $this->createUrl("/web/userdata/payment"); ?>" >Payment<span><?php echo Yii::app()->user->user->sellerPayment . " &euro;"; ?></span></a>
    </li>
    <li class="<?php echo $this->action->id == "ratings" ? "active" : ""; ?>">
        <a href="<?php echo $this->createUrl("/web/userdata/ratings"); ?>" >Ratings<span><?php echo Yii::app()->user->user->getRatings(); ?></span></a>
    </li>

</ul>