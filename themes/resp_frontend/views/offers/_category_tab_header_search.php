<div class="tab-header">
    <?php
    $data = BspCategory::model()->findAll(array('condition' => 'parent_id=0'));
    foreach ($data as $cat):
        echo CHtml::link($cat->name, $this->createUrl("/web/offers/category", array("category" => $cat->slug)));
    endforeach;
    ?>
</div>