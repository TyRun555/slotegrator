<?php

namespace app\models;

use app\models\base\BaseAR;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "prize".
 *
 * @property int $id
 * @property int $title
 * @property int $description
 * @property int|null $status
 *
 * @property PrizeLog[] $prizeLogs
 */
class Prize extends BaseAR
{
    /** @var int - prize available to be won */
    const STATUS_AVAILABLE = 1;
    /** @var int - prize hase been won */
    const STATUS_WON = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'prize';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['title', 'description', 'status'], 'required'],
            [['status'], 'in', 'range' => [self::STATUS_AVAILABLE, self::STATUS_WON]],
            [['title'], 'string', 'max' => 255],
            [['description'], 'string']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Title'),
            'description' => Yii::t('app', 'Description'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * Gets query for [[PrizeLogs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrizeLogs(): ActiveQuery
    {
        return $this->hasMany(PrizeLog::class, ['prize_id' => 'id']);
    }
}
