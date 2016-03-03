<?php

namespace Venice\AppBundle\DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 */
class Version20160303165107 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('DROP INDEX unique_name ON trinity_settings');
        $this->addSql('ALTER TABLE trinity_settings ADD group_name VARCHAR(64) DEFAULT NULL, DROP groupName');
        $this->addSql('CREATE UNIQUE INDEX unique_name_group ON trinity_settings (name, group_name)');
        $this->addSql('CREATE UNIQUE INDEX unique_name ON trinity_settings (name)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql('DROP INDEX unique_name_group ON trinity_settings');
        $this->addSql('DROP INDEX unique_name ON trinity_settings');
        $this->addSql('ALTER TABLE trinity_settings ADD groupName LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, DROP group_name');
        $this->addSql('CREATE UNIQUE INDEX unique_name ON trinity_settings (name)');
    }
}
