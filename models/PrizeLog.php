<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "prize_log".
 *
 * @property int $id
 * @property int $user_id
 * @property int $prize_id
 * @property int $created_at
 *
 * @property Prize $prize
 * @property User $user
 */
class PrizeLog extends \app\models\base\BaseAR
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'prize_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'prize_id', 'created_at'], 'required'],
            [['user_id', 'prize_id', 'created_at'], 'integer'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['prize_id'], 'exist', 'skipOnError' => true, 'targetClass' => Prize::class, 'targetAttribute' => ['prize_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'prize_id' => Yii::t('app', 'Prize ID'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * Gets query for [[Prize]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrize()
    {
        return $this->hasOne(Prize::class, ['id' => 'prize_id'])->inverseOf('prizeLogs');
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id'])->inverseOf('prizeLogs');
    }
}
