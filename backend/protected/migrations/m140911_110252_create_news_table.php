<?php

class m140911_110252_create_news_table extends CDbMigration {

    public function up() {
        $this->createTable('gxc_news', array(
            'id' => 'pk',
            'title' => 'string NOT NULL',
            'content' => 'text',
        ));
    }

    public function down() {
        $this->dropTable('gxc_news');
    }

    /*
      // Use safeUp/safeDown to do migration with transaction
      public function safeUp()
      {
      }

      public function safeDown()
      {
      }
     */
}
