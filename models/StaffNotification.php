<?php

namespace app\models;

use JetBrains\PhpStorm\ArrayShape;
use Yii;
use yii\db\ActiveQuery;
use yii\web\View;

/**
 * This is the model class for table "staff_notification".
 *
 * @property int $id
 * @property int $user_id
 * @property int $message_template
 * @property string|null $data
 * @property int|null $status
 * @property int|null $created_at
 * @property int|null $sent_at
 *
 * @property User $user
 */
class StaffNotification extends \app\models\base\BaseAR
{
    //region Template Constants
    const TEMPLATE_PRIZE_ITEM = '/staff/notifications/prize/item';
    //endregion

    //region Active Record Methods
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'staff_notification';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['user_id', 'message_template'], 'required'],
            [['user_id', 'message_template', 'status', 'created_at', 'sent_at'], 'integer'],
            [['data'], 'safe'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    #[ArrayShape(['id' => "string", 'user_id' => "string", 'message_template' => "string", 'data' => "string", 'created_at' => "string", 'sent_at' => "string"])]
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'message_template' => Yii::t('app', 'Message Template'),
            'data' => Yii::t('app', 'Data'),
            'created_at' => Yii::t('app', 'Created At'),
            'sent_at' => Yii::t('app', 'Sent At'),
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert) {
            /**
             * send notification to staff emails
             */
            Yii::$app->mailer->compose()
                ->setFrom(Yii::$app->params['adminEmail'])
                ->setTo(Yii::$app->params['staffEmail'])
                ->setSubject(Yii::$app->name . ": need action")
                ->setHtmlBody((new View)->render($this->message_template, ['notification' => $this]))
                ->send();
        }
    }
    //endregion

    //region Relations
    /**
     * Gets query for [[User]].
     *
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id'])->inverseOf('staffNotifications');
    }
    //endregion
}
