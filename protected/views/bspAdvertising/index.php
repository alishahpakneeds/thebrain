<?php
/* @var $this BspAdvertisingController */
/* @var $model BspAdvertising */

$this->breadcrumbs = array(
    'Bsp Advertisings' => array('index'),
    'Manage',
);

$this->menu = array(
    array('label' => 'List BspAdvertising', 'url' => array('index')),
    array('label' => 'Create BspAdvertising', 'url' => array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#bsp-advertising-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Bsp Advertisings</h1>

<p>
    You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
    or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo CHtml::link('Advanced Search', '#', array('class' => 'search-button')); ?>
<div class="search-form" style="display:none">
    <?php
    $this->renderPartial('_search', array(
        'model' => $model,
    ));
    ?>
</div><!-- search-form -->

<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'bsp-advertising-grid',
    'dataProvider' => $model->search(),
    'cssFile' => Yii::app()->theme->baseUrl . "/assets/css/gridview.css",
    'pager' => array(
        'cssFile' => '',
    ),
    'filter' => $model,
    'columns' => array(
        'adv_name',
        'adv_img',
        'adv_url',
        'adv_position',
        'iStatus',
        array(
            'class' => 'CButtonColumn',
        ),
    ),
));
?>
