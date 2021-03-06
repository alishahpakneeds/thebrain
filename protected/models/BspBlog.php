<?php

/**
 * This is the model class for table "bsp_blog".
 *
 * The followings are the available columns in table 'bsp_blog':
 * @property integer $id
 * @property string $user_id
 * @property string $title
 * @property string $img
 * @property string $description
 * @property string $detail
 * @property string $date_create
 * @property string $create_time
 * @property string $create_user_id
 * @property string $update_time
 * @property string $update_user_id
 */
class BspBlog extends DTActiveRecord {

    public $slug;

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'bsp_blog';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('title, create_time, create_user_id, update_time, update_user_id', 'required'),
            array('user_id', 'length', 'max' => 45),
            array('img', 'file', 'allowEmpty' => $this->isNewRecord ? false : true,
                'types' => 'jpg,jpeg,gif,png,JPG,JPEG,GIF,PNG'),
            array('title, ', 'length', 'max' => 255),
            array('create_user_id, update_user_id', 'length', 'max' => 11),
            array('description, detail, date_create', 'safe'),
            array('slug', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, user_id, title, img, description, detail, date_create, create_time, create_user_id, update_time, update_user_id', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'user' => array(self::BELONGS_TO, 'Users', 'user_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'user_id' => 'User',
            'title' => 'Title',
            'img' => 'Image',
            'description' => 'Description',
            'detail' => 'Detail',
            'date_create' => 'Date Create',
            'create_time' => 'Create Time',
            'create_user_id' => 'Create User',
            'update_time' => 'Update Time',
            'update_user_id' => 'Update User',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search() {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('user_id', $this->user_id, true);
        $criteria->compare('title', $this->title, true);
        $criteria->compare('img', $this->img, true);
        $criteria->compare('description', $this->description, true);
        $criteria->compare('detail', $this->detail, true);
        $criteria->compare('date_create', $this->date_create, true);
        $criteria->compare('create_time', $this->create_time, true);
        $criteria->compare('create_user_id', $this->create_user_id, true);
        $criteria->compare('update_time', $this->update_time, true);
        $criteria->compare('update_user_id', $this->update_user_id, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * 
     */
    public function beforeValidate() {
        parent::beforeValidate();
        $this->date_create = $this->create_time;
        return true;
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return BspBlog the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * slug 
     */
    public function afterFind() {
        $this->setSlug();
        return parent::afterFind();
    }

    /**
     * set slug for 
     * urls
     */
    public function setSlug() {
        $this->slug = $this->primaryKey . "-" . str_replace(Yii::app()->params['notallowdCharactorsUrl'], "", trim($this->title));
        $this->slug = strtolower(str_replace(" ", "-", trim($this->slug)));
    }

}
