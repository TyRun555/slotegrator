<?php
namespace app\migrations;

use yii\db\Migration;

/**
 * Handles the creation of table `{{%settings}}`.
 */
class m210905_205531_create_settings_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%settings}}', [
            'id' => $this->primaryKey(),
            'money_pool_amount' => $this->integer()->defaultValue(0),
            'money_pool_amount_reserved' => $this->integer()->defaultValue(0),
        ]);
        $this->insert('{{%settings}}', ['money_pool_amount' => $_ENV['START_MONEY_POOL']]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%settings}}');
    }
}
