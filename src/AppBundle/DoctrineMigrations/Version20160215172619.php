<?php

namespace AppBundle\DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 */
class Version20160215172619 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('CREATE TABLE blog_article_product (blog_article_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_8C613C999452A475 (blog_article_id), INDEX IDX_8C613C994584665A (product_id), PRIMARY KEY(blog_article_id, product_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE blog_article_product ADD CONSTRAINT FK_8C613C999452A475 FOREIGN KEY (blog_article_id) REFERENCES blog_article (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blog_article_product ADD CONSTRAINT FK_8C613C994584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('DROP INDEX UNIQ_A22865BA4E31C068 ON billing_plan');
        $this->addSql('ALTER TABLE billing_plan DROP amember_id');
        $this->addSql('ALTER TABLE product_standard DROP FOREIGN KEY FK_DB47BD109A5987E');
        $this->addSql('ALTER TABLE product_standard DROP FOREIGN KEY FK_DB47BD10C45F6E92');
        $this->addSql('DROP INDEX UNIQ_DB47BD104E31C068 ON product_standard');
        $this->addSql('DROP INDEX UNIQ_DB47BD10C45F6E92 ON product_standard');
        $this->addSql('DROP INDEX UNIQ_DB47BD109A5987E ON product_standard');
        $this->addSql('ALTER TABLE product_standard ADD default_billing_plan_id INT DEFAULT NULL, DROP mobile_billing_plan_id, DROP desktop_billing_plan_id, DROP amember_id');
        $this->addSql('ALTER TABLE product_standard ADD CONSTRAINT FK_DB47BD101BE12190 FOREIGN KEY (default_billing_plan_id) REFERENCES billing_plan (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DB47BD101BE12190 ON product_standard (default_billing_plan_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE blog_article_product');
        $this->addSql('ALTER TABLE billing_plan ADD amember_id INT DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A22865BA4E31C068 ON billing_plan (amember_id)');
        $this->addSql('ALTER TABLE product_access CHANGE from_date from_date DATETIME NOT NULL, CHANGE to_date to_date DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE product_standard DROP FOREIGN KEY FK_DB47BD101BE12190');
        $this->addSql('DROP INDEX UNIQ_DB47BD101BE12190 ON product_standard');
        $this->addSql('ALTER TABLE product_standard ADD desktop_billing_plan_id INT DEFAULT NULL, ADD amember_id INT DEFAULT NULL, CHANGE default_billing_plan_id mobile_billing_plan_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product_standard ADD CONSTRAINT FK_DB47BD109A5987E FOREIGN KEY (mobile_billing_plan_id) REFERENCES billing_plan (id)');
        $this->addSql('ALTER TABLE product_standard ADD CONSTRAINT FK_DB47BD10C45F6E92 FOREIGN KEY (desktop_billing_plan_id) REFERENCES billing_plan (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DB47BD104E31C068 ON product_standard (amember_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DB47BD10C45F6E92 ON product_standard (desktop_billing_plan_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DB47BD109A5987E ON product_standard (mobile_billing_plan_id)');
    }
}
