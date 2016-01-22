<?php

namespace AppBundle\DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160120133503 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE dmishh_settings (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, value LONGTEXT DEFAULT NULL, owner_id VARCHAR(255) DEFAULT NULL, INDEX name_owner_id_idx (name, owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE billing_plan (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, necktie_id INT DEFAULT NULL, amember_id INT DEFAULT NULL, initial_price DOUBLE PRECISION NOT NULL, rebill_price DOUBLE PRECISION NOT NULL, frequency INT NOT NULL, rebill_times INT NOT NULL, price VARCHAR(50) NOT NULL, UNIQUE INDEX UNIQ_A22865BA7CEC66E1 (necktie_id), UNIQUE INDEX UNIQ_A22865BA4E31C068 (amember_id), INDEX IDX_A22865BA4584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE blog_article (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, handle VARCHAR(255) NOT NULL, date_written DATETIME NOT NULL, date_to_publish DATETIME DEFAULT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, UNIQUE INDEX UNIQ_EECCB3E5918020D9 (handle), INDEX IDX_EECCB3E5A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE content (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_FEC530A95E237E06 (name), INDEX IDX_FEC530A9F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE content_in_group (id INT AUTO_INCREMENT NOT NULL, group_id INT DEFAULT NULL, content_id INT DEFAULT NULL, delay INT NOT NULL, order_number INT NOT NULL, INDEX IDX_7582B054FE54D947 (group_id), INDEX IDX_7582B05484A0A3ED (content_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE content_group (id INT NOT NULL, handle VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_86031017918020D9 (handle), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE content_html (id INT NOT NULL, html LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE content_mp3 (id INT NOT NULL, link VARCHAR(255) NOT NULL, duration INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE content_pdf (id INT NOT NULL, link VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE content_text (id INT NOT NULL, text LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE content_video (id INT NOT NULL, preview_image VARCHAR(255) NOT NULL, video_mob VARCHAR(255) DEFAULT NULL, video_lq VARCHAR(255) DEFAULT NULL, video_hq VARCHAR(255) DEFAULT NULL, video_hd VARCHAR(255) DEFAULT NULL, duration INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contents_products (id INT AUTO_INCREMENT NOT NULL, content_id INT DEFAULT NULL, product_id INT DEFAULT NULL, delay INT NOT NULL, order_number INT NOT NULL, INDEX IDX_F8FCB46784A0A3ED (content_id), INDEX IDX_F8FCB4674584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE oauth_token (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, access_token VARCHAR(255) NOT NULL, refresh_token VARCHAR(255) NOT NULL, valid_to DATETIME NOT NULL, scope VARCHAR(255) DEFAULT NULL, INDEX IDX_D8344B2AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, handle VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL, enabled TINYINT(1) NOT NULL, order_number INT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, type VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_D34A04AD918020D9 (handle), UNIQUE INDEX UNIQ_D34A04AD5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_free (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_standard (id INT NOT NULL, desktop_billing_plan_id INT DEFAULT NULL, mobile_billing_plan_id INT DEFAULT NULL, necktie_id INT DEFAULT NULL, amember_id INT DEFAULT NULL, notification_status LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_DB47BD107CEC66E1 (necktie_id), UNIQUE INDEX UNIQ_DB47BD104E31C068 (amember_id), UNIQUE INDEX UNIQ_DB47BD10C45F6E92 (desktop_billing_plan_id), UNIQUE INDEX UNIQ_DB47BD109A5987E (mobile_billing_plan_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_access (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, product_id INT DEFAULT NULL, necktie_id INT DEFAULT NULL, from_date DATETIME NOT NULL, to_date DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_64156AF07CEC66E1 (necktie_id), INDEX IDX_64156AF0A76ED395 (user_id), INDEX IDX_64156AF04584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, username_canonical VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, email_canonical VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, locked TINYINT(1) NOT NULL, expired TINYINT(1) NOT NULL, expires_at DATETIME DEFAULT NULL, confirmation_token VARCHAR(255) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', credentials_expired TINYINT(1) NOT NULL, credentials_expire_at DATETIME DEFAULT NULL, necktie_id INT DEFAULT NULL, amember_id INT DEFAULT NULL, preferred_units VARCHAR(10) NOT NULL, date_of_birth DATE NOT NULL, status LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, phone_number VARCHAR(255) DEFAULT NULL, website VARCHAR(255) DEFAULT NULL, avatar VARCHAR(255) DEFAULT NULL, public TINYINT(1) DEFAULT NULL, country VARCHAR(2) DEFAULT NULL, region VARCHAR(32) DEFAULT NULL, city VARCHAR(32) DEFAULT NULL, address_line1 VARCHAR(255) DEFAULT NULL, address_line2 VARCHAR(255) DEFAULT NULL, postal_code VARCHAR(10) DEFAULT NULL, notification_status LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_8D93D64992FC23A8 (username_canonical), UNIQUE INDEX UNIQ_8D93D649A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_8D93D6497CEC66E1 (necktie_id), UNIQUE INDEX UNIQ_8D93D6494E31C068 (amember_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cron_task (id INT AUTO_INCREMENT NOT NULL, command VARCHAR(255) NOT NULL, creation_time DATETIME NOT NULL, processing_time DATETIME DEFAULT NULL, priority INT NOT NULL, status VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE billing_plan ADD CONSTRAINT FK_A22865BA4584665A FOREIGN KEY (product_id) REFERENCES product_standard (id)');
        $this->addSql('ALTER TABLE blog_article ADD CONSTRAINT FK_EECCB3E5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE content ADD CONSTRAINT FK_FEC530A9F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE content_in_group ADD CONSTRAINT FK_7582B054FE54D947 FOREIGN KEY (group_id) REFERENCES content_group (id)');
        $this->addSql('ALTER TABLE content_in_group ADD CONSTRAINT FK_7582B05484A0A3ED FOREIGN KEY (content_id) REFERENCES content (id)');
        $this->addSql('ALTER TABLE content_group ADD CONSTRAINT FK_86031017BF396750 FOREIGN KEY (id) REFERENCES content (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE content_html ADD CONSTRAINT FK_D51B1520BF396750 FOREIGN KEY (id) REFERENCES content (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE content_mp3 ADD CONSTRAINT FK_9A4ACF74BF396750 FOREIGN KEY (id) REFERENCES content (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE content_pdf ADD CONSTRAINT FK_BB1B8D79BF396750 FOREIGN KEY (id) REFERENCES content (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE content_text ADD CONSTRAINT FK_F6E94A02BF396750 FOREIGN KEY (id) REFERENCES content (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE content_video ADD CONSTRAINT FK_97048EFEBF396750 FOREIGN KEY (id) REFERENCES content (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE contents_products ADD CONSTRAINT FK_F8FCB46784A0A3ED FOREIGN KEY (content_id) REFERENCES content (id)');
        $this->addSql('ALTER TABLE contents_products ADD CONSTRAINT FK_F8FCB4674584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE oauth_token ADD CONSTRAINT FK_D8344B2AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE product_free ADD CONSTRAINT FK_C0C6E369BF396750 FOREIGN KEY (id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_standard ADD CONSTRAINT FK_DB47BD10C45F6E92 FOREIGN KEY (desktop_billing_plan_id) REFERENCES billing_plan (id)');
        $this->addSql('ALTER TABLE product_standard ADD CONSTRAINT FK_DB47BD109A5987E FOREIGN KEY (mobile_billing_plan_id) REFERENCES billing_plan (id)');
        $this->addSql('ALTER TABLE product_standard ADD CONSTRAINT FK_DB47BD10BF396750 FOREIGN KEY (id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_access ADD CONSTRAINT FK_64156AF0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE product_access ADD CONSTRAINT FK_64156AF04584665A FOREIGN KEY (product_id) REFERENCES product (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE product_standard DROP FOREIGN KEY FK_DB47BD10C45F6E92');
        $this->addSql('ALTER TABLE product_standard DROP FOREIGN KEY FK_DB47BD109A5987E');
        $this->addSql('ALTER TABLE content_in_group DROP FOREIGN KEY FK_7582B05484A0A3ED');
        $this->addSql('ALTER TABLE content_group DROP FOREIGN KEY FK_86031017BF396750');
        $this->addSql('ALTER TABLE content_html DROP FOREIGN KEY FK_D51B1520BF396750');
        $this->addSql('ALTER TABLE content_mp3 DROP FOREIGN KEY FK_9A4ACF74BF396750');
        $this->addSql('ALTER TABLE content_pdf DROP FOREIGN KEY FK_BB1B8D79BF396750');
        $this->addSql('ALTER TABLE content_text DROP FOREIGN KEY FK_F6E94A02BF396750');
        $this->addSql('ALTER TABLE content_video DROP FOREIGN KEY FK_97048EFEBF396750');
        $this->addSql('ALTER TABLE contents_products DROP FOREIGN KEY FK_F8FCB46784A0A3ED');
        $this->addSql('ALTER TABLE content_in_group DROP FOREIGN KEY FK_7582B054FE54D947');
        $this->addSql('ALTER TABLE contents_products DROP FOREIGN KEY FK_F8FCB4674584665A');
        $this->addSql('ALTER TABLE product_free DROP FOREIGN KEY FK_C0C6E369BF396750');
        $this->addSql('ALTER TABLE product_standard DROP FOREIGN KEY FK_DB47BD10BF396750');
        $this->addSql('ALTER TABLE product_access DROP FOREIGN KEY FK_64156AF04584665A');
        $this->addSql('ALTER TABLE billing_plan DROP FOREIGN KEY FK_A22865BA4584665A');
        $this->addSql('ALTER TABLE blog_article DROP FOREIGN KEY FK_EECCB3E5A76ED395');
        $this->addSql('ALTER TABLE content DROP FOREIGN KEY FK_FEC530A9F675F31B');
        $this->addSql('ALTER TABLE oauth_token DROP FOREIGN KEY FK_D8344B2AA76ED395');
        $this->addSql('ALTER TABLE product_access DROP FOREIGN KEY FK_64156AF0A76ED395');
        $this->addSql('ALTER TABLE acl_entries DROP FOREIGN KEY FK_46C8B806EA000B10');
        $this->addSql('ALTER TABLE acl_entries DROP FOREIGN KEY FK_46C8B806DF9183C9');
        $this->addSql('ALTER TABLE acl_object_identities DROP FOREIGN KEY FK_9407E54977FA751A');
        $this->addSql('ALTER TABLE acl_object_identity_ancestors DROP FOREIGN KEY FK_825DE2993D9AB4A6');
        $this->addSql('ALTER TABLE acl_object_identity_ancestors DROP FOREIGN KEY FK_825DE299C671CEA1');
        $this->addSql('ALTER TABLE acl_entries DROP FOREIGN KEY FK_46C8B8063D9AB4A6');
        $this->addSql('DROP TABLE dmishh_settings');
        $this->addSql('DROP TABLE billing_plan');
        $this->addSql('DROP TABLE blog_article');
        $this->addSql('DROP TABLE content');
        $this->addSql('DROP TABLE content_in_group');
        $this->addSql('DROP TABLE content_group');
        $this->addSql('DROP TABLE content_html');
        $this->addSql('DROP TABLE content_mp3');
        $this->addSql('DROP TABLE content_pdf');
        $this->addSql('DROP TABLE content_text');
        $this->addSql('DROP TABLE content_video');
        $this->addSql('DROP TABLE contents_products');
        $this->addSql('DROP TABLE oauth_token');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_free');
        $this->addSql('DROP TABLE product_standard');
        $this->addSql('DROP TABLE product_access');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE cron_task');
        $this->addSql('DROP TABLE acl_classes');
        $this->addSql('DROP TABLE acl_security_identities');
        $this->addSql('DROP TABLE acl_object_identities');
        $this->addSql('DROP TABLE acl_object_identity_ancestors');
        $this->addSql('DROP TABLE acl_entries');
    }
}
