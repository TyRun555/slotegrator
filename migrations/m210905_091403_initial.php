<?php
namespace app\migrations;

use app\models\User;
use yii\db\Migration;

/**
 * Class m210905_091403_initial
 */
class m210905_091403_initial extends Migration
{
    /**
     * {@inheritdoc}
     * @throws \yii\base\Exception
     */
    public function safeUp()
    {
        //region User Identity Table
        $this->createTable('users', [
            'id' => $this->primaryKey(),
            'email' => $this->string()->notNull()->unique(),
            'username' => $this->string()->notNull()->unique(),
            'password' => $this->string()->notNull(),
            'auth_key' => $this->string()->unique(),
            'access_token' => $this->string()->unique(),
            'password_reset_token' => $this->string(),
            'role' => $this->integer(1)->defaultValue(User::ROLE_USER)->notNull(),
            'status' => $this->integer(1)->defaultValue(User::STATUS_ACTIVE),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer()
        ]);

        $this->createIndex('IDX-users-created_at', 'users', 'created_at');
        $this->createIndex('IDX-users-updated_at', 'users', 'created_at');
        $this->execute("ALTER TABLE `users` ADD FULLTEXT INDEX `IDX-users-username` (username)");
        //endregion

        //region User Account Tables
        $this->createTable('user_account', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull()->unique(),
            'amount' => $this->integer()->defaultValue(0)->unsigned()
        ]);

        $this->addForeignKey(
            'FK-user_account-user_id',
            'user_account',
            'user_id',
            'users',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->createTable('user_account_transactions', [
            'id' => $this->primaryKey(),
            'account_id' => $this->integer()->notNull()->unique(),
            'created_at' => $this->integer(),
            'amount_change' => $this->integer()->unsigned(),
            'type' => $this->integer(1),
            'direction' => $this->integer(1)
        ]);

        $this->addForeignKey(
            'FK-user_account_transactions-account_id',
            'user_account_transactions',
            'account_id',
            'user_account',
            'id',
            'CASCADE',
            'CASCADE'
        );
        //endregion

        //region Prizes tables
        $this->createTable('prize', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull(),
            'description' => $this->text(),
            'status' => $this->integer(1)->notNull()
        ]);

        $this->createTable('prize_log', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'prize_hash' => $this->string()->notNull(),
            'prize_type' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull()
        ]);

        $this->createIndex('IDX-prize_log-created_at', 'prize_log', 'created_at');
        $this->addForeignKey(
            'FK-prize_log-user_id',
            'prize_log',
            'user_id',
            'users',
            'id',
            'CASCADE',
            'CASCADE'
        );
        //endregion

        //region Notifications table (for staff)
        $this->createTable('staff_notification', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'message_template' => $this->integer()->notNull(),
            'data' => $this->json(),
            'status' => $this->integer(1),
            'created_at' => $this->integer(),
            'sent_at' => $this->integer()
        ]);

        $this->addForeignKey(
            'FK-staff_notification-user_id',
            'staff_notification',
            'user_id',
            'users',
            'id',
            'NO ACTION',
            'CASCADE'
        );
        //endregion
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('staff_notification');
        $this->dropTable('prize_log');
        $this->dropTable('prize');
        $this->dropTable('user_account_transactions');
        $this->dropTable('user_account');
        $this->dropTable('users');
    }

}
