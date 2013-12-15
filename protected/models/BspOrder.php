<?php

/**
 * This is the model class for table "bsp_order".
 *
 * The followings are the available columns in table 'bsp_order':
 * @property integer $id
 * @property integer $item_id
 * @property integer $buyer_id
 * @property integer $seller_id
 * @property string $date_order
 * @property string $date_start
 * @property string $date_finish
 * @property string $description
 * @property double $pph
 * @property double $comission
 * @property double $amount
 * @property integer $payment
 * @property integer $status
 * @property string $create_time
 * @property string $create_user_id
 * @property string $update_time
 * @property string $update_user_id
 */
class BspOrder extends DTActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'bsp_order';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('item_id, buyer_id, seller_id, pph, comission, amount, payment, status, create_time, create_user_id, update_time, update_user_id', 'required'),
            array('item_id, buyer_id, seller_id, payment, status', 'numerical', 'integerOnly' => true),
            array('pph, comission, amount', 'numerical'),
            array('description', 'length', 'max' => 300),
            array('create_user_id, update_user_id', 'length', 'max' => 11),
            array('date_order, date_start, date_finish', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, item_id, buyer_id, seller_id, date_order, date_start, date_finish, description, pph, comission, amount, payment, status, create_time, create_user_id, update_time, update_user_id', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'item' => array(self::BELONGS_TO, 'BspItem', 'item_id'),
            'buyer' => array(self::BELONGS_TO, 'Users', 'buyer_id'),
            'seller' => array(self::BELONGS_TO, 'Users', 'seller_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'item_id' => 'Item',
            'buyer_id' => 'Buyer',
            'seller_id' => 'Seller',
            'date_order' => 'Date Order',
            'date_start' => 'Date Start',
            'date_finish' => 'Date Finish',
            'description' => 'Description',
            'pph' => 'Pph',
            'comission' => 'Comission',
            'amount' => 'Amount',
            'payment' => 'Payment',
            'status' => 'Status',
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
        $criteria->compare('item_id', $this->item_id);
        $criteria->compare('buyer_id', $this->buyer_id);
        $criteria->compare('seller_id', $this->seller_id);
        $criteria->compare('date_order', $this->date_order, true);
        $criteria->compare('date_start', $this->date_start, true);
        $criteria->compare('date_finish', $this->date_finish, true);
        $criteria->compare('description', $this->description, true);
        $criteria->compare('pph', $this->pph);
        $criteria->compare('comission', $this->comission);
        $criteria->compare('amount', $this->amount);
        $criteria->compare('payment', $this->payment);
        $criteria->compare('status', $this->status);
        $criteria->compare('create_time', $this->create_time, true);
        $criteria->compare('create_user_id', $this->create_user_id, true);
        $criteria->compare('update_time', $this->update_time, true);
        $criteria->compare('update_user_id', $this->update_user_id, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return BspOrder the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

}
