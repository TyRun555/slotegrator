<?php

namespace app\models\form;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class PrizeDeliveryForm extends Model
{
    public $country;
    public $zip;
    public $city;
    public $street;
    public $building;
    public $room;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['country', 'zip', 'city', 'street', 'building', 'room'], 'required'],
            [['country', 'zip', 'city', 'street', 'building', 'room'], 'string'],
            ['verifyCode', 'captcha'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'verifyCode' => 'Verification Code',
        ];
    }
}
