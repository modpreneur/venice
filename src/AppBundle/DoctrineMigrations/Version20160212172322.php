<?php

namespace AppBundle\DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160212172322 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE blog_article_product (blog_article_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_8C613C999452A475 (blog_article_id), INDEX IDX_8C613C994584665A (product_id), PRIMARY KEY(blog_article_id, product_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE blog_article_product ADD CONSTRAINT FK_8C613C999452A475 FOREIGN KEY (blog_article_id) REFERENCES blog_article (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blog_article_product ADD CONSTRAINT FK_8C613C994584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_access CHANGE from_date from_date DATETIME NOT NULL, CHANGE to_date to_date DATETIME DEFAULT NULL');
        $this->addSql('DROP INDEX unique_name ON trinity_settings');
        $this->addSql('CREATE UNIQUE INDEX unique_name ON trinity_settings (name)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE blog_article_product');
        $this->addSql('ALTER TABLE product_access CHANGE from_date from_date DATETIME NOT NULL, CHANGE to_date to_date DATETIME DEFAULT NULL');
        $this->addSql('DROP INDEX unique_name ON trinity_settings');
        $this->addSql('CREATE UNIQUE INDEX unique_name ON trinity_settings (name)');
    }
}
