<?php
/* @var $this BspOrderController */
/* @var $model BspOrder */

$this->breadcrumbs = array(
    'Payments',
);

$this->PcmWidget['filter'] = array('name' => 'ItstLeftFilter',
    'attributes' => array(
        'model' => $model,
        'filters' => $this->filters,
        'keyUrl' => true,
        'action' => Yii::app()->createUrl($this->route),
        'grid_id' => 'payment-grid',
        "view" => "paymentNotifications"
        ));
?>

<h1>Manage Payments</h1>

<?php
if (Yii::app()->user->hasFlash('error')) {
    echo "<span class='alert alert-error'>" . Yii::app()->user->getFlash('error') . "</span>";
    echo CHtml::tag("div", array('class' => "clear"),false);
}
if (Yii::app()->user->hasFlash('success')) {
    echo "<span class='alert alert-success'>" . Yii::app()->user->getFlash('success') . "</span>";
    echo CHtml::tag("div", array('class' => "clear"),false);
}
?>

<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'admin-payment-form',
        ));
?>
<p class="note">
    <?php
    echo $form->hiddenField($transfer_Model, "flag", array("value" => "1"));
    echo $form->error($transfer_Model, 'selection');
    ?>
</p>

<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'payment-grid',
    'dataProvider' => $dataProvider,
    'filter' => $model,
    'cssFile' => Yii::app()->theme->baseUrl . "/assets/css/gridview.css",
    'pager' => array(
        'cssFile' => '',
    ),
    'columns' => array(
        'id:html' => array(
            'header' => '<input type="checkbox"  id="header" onclick="
                thepuzzleadmin.checkUncheckBoxAll(this);
            "/>',
            "type" => "raw",
            // "class"=>'($data->_transfer_status ==1)?CCheckBoxColumn:""',
            'value' => '($data->_transfer_status ==1)?CHtml::checkBox("id[]",$data->id,array("value"=>$data->id,"id"=>"id_".$data->id)):""',
            "htmlOptions" => array("class" => "child_checkbox"),
            //"class"=>"CCheckBoxColumn"
        ),
        array(
            'name' => 'item_id',
            "type" => "raw",
            'value' => '!empty($data->offer)?CHtml::link($data->offer->name,Yii::app()->controller->createUrl("/item/view",array("id"=>$data->id))):""',
        ),
        array('name' => 'buyer_id', 'value' => '!empty($data->buyer)?$data->buyer->_name:""',),
        array('name' => 'sender_id', 'value' => '!empty($data->seller)?$data->seller->_name:""',),
        array('name' => 'sender_id', 'value' => '!empty($data->seller)?$data->seller->_name:""',),
        array('name' => 'amount', 'value' => '$data->amount',),
        array('name' => 'puzzzle_commission', 'value' => '$data->puzzzle_commission',),
        array('name' => '_transfer_amount', 'value' => '$data->amount -  $data->puzzzle_commission',),
        'buyer_status',
        'seller_status',
        'puzzzle_admin_status',
        'ip_address',
    ),
));

echo CHtml::submitButton('Transfer Money', array('class' => 'btn btn btn-primary'));
$this->endWidget();
?>
