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
            'money_amount_rest' => $this->integer()
        ]);
        $this->insert('{{%settings}}', ['money_amount_rest' => $_ENV['START_MONEY_POOL']]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%settings}}');
    }
}
