<?php

namespace app\models;

use app\models\base\BaseAR;
use JetBrains\PhpStorm\ArrayShape;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "prize_log".
 * Contains  prize winning history by user
 * TODO: apply this model in future, currently not used
 *
 * @property int $id
 * @property int $user_id
 * @property int $created_at
 *
 * @property Prize $prize
 * @property User $user
 * @property string $prize_hash [varchar(255)]
 * @property int $prize_type [int]
 */
class PrizeLog extends BaseAR
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'prize_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['user_id', 'prize_id', 'created_at'], 'required'],
            [['user_id', 'prize_id', 'created_at'], 'integer'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['prize_id'], 'exist', 'skipOnError' => true, 'targetClass' => Prize::class, 'targetAttribute' => ['prize_id' => 'id']],
        ];
    }

    public function behaviors(): array
    {
        return array_merge(parent::behaviors(), [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
                'value' => time()
            ]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
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
     * @return ActiveQuery
     */
    public function getPrize(): ActiveQuery
    {
        return $this->hasOne(Prize::class, ['id' => 'prize_id'])->inverseOf('prizeLogs');
    }

    /**
     * Gets query for [[User]].
     *
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id'])->inverseOf('prizeLogs');
    }
}
