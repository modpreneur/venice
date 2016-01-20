<?php

namespace AppBundle\DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160120133620 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('INSERT INTO `user` (`id`, `username`, `username_canonical`, `email`, `email_canonical`, `enabled`, `salt`, `password`, `last_login`, `locked`, `expired`, `expires_at`, `confirmation_token`, `password_requested_at`, `roles`, `credentials_expired`, `credentials_expire_at`, `necktie_id`, `amember_id`, `preferred_units`, `date_of_birth`, `first_name`, `last_name`, `phone_number`, `website`, `avatar`, `public`, `country`, `region`, `city`, `address_line1`, `address_line2`, `postal_code`, `status`, `notification_status`) VALUES(1,\'superAdmin\',\'superadmin\',\'superAdmin@webvalley.cz\',\'superadmin@webvalley.cz\',1,\'\',\'$2y$13$s.dN44IbfD89ULfYZ8fh.eqHXuoewyrRH/irja9o6/jac8xMOpqPC\',\'2016-01-06 11:55:35\',0,0,NULL,NULL,NULL,\'a:2:{i:0;s:9:\"ROLE_USER\";i:1;s:16:\"ROLE_SUPER_ADMIN\";}\',0,NULL,2,NULL,\'imperial\',\'2015-12-10\',\'1Supeross1ssqq\',\'Adminosssssaaa\',\'312313\',\'http://www.shit.com\',NULL,1,\'SO\',\'13231313\',\'1231231231\',\'1313\',\'1323\',\'12313113\',NULL,NULL);');
        $this->addSql('INSERT INTO `content` (`id`, `author_id`, `name`, `type`) VALUES (30,NULL,\'Workout #5\',\'videocontent\'),(35,NULL,\'Workout #6\',\'videocontent\'),(39,NULL,\'Starting...\',\'videocontent\'),(40,NULL,\'Workout #1\',\'videocontent\'),(41,NULL,\'Workout #2\',\'videocontent\'),(42,NULL,\'Workout #3\',\'videocontent\'),(43,NULL,\'Workout #4\',\'videocontent\'),(44,NULL,\'Cookbook #1\',\'pdfcontent\'),(45,NULL,\'Cookbook #2\',\'pdfcontent\'),(46,NULL,\'Calendar #1\',\'pdfcontent\'),(47,NULL,\'Calendar #2\',\'pdfcontent\'),(48,NULL,\'Motivating podcast\',\'mp3content\');');
        $this->addSql('INSERT INTO `content_mp3` (`id`, `link`, `duration`) VALUES(48,\'http://mo.ti.va/ting\',324);');
        $this->addSql('INSERT INTO `content_pdf` (`id`, `link`) VALUES(44,\'http://www.cook.book/1\'),(45,\'http://www.cook.book/2\'),(46,\'http://www.calend.ar/1\'),(47,\'http://www.calend.ar/2\');');
        $this->addSql('INSERT INTO `content_video` (`id`, `preview_image`, `video_mob`, `video_lq`, `video_hq`, `video_hd`, `duration`) VALUES 	(30,\'http://www.vvv.cz\',\'http://www.vvv.czmob\',\'http://www.vvv.czlq\',\'http://www.vvv.czhq\',\'http://www.vvv.czhd\',233),	(35,\'http://www.ggg.ccc\',\'http://www.vvv.czmob\',\'http://www.vvv.czlq\',\'http://www.vvv.czhq\',\'http://www.vvv.czhd\',333),	(39,\'http://www.ggg.ccc\',\'http://www.ggg.ccc\',\'http://www.ggg.ccc\',\'http://www.ggg.ccc\',\'http://www.ggg.ccc\',203),	(40,\'http://www.ggg.ccc\',\'http://www.ggg.ccc\',\'http://www.ggg.ccc\',\'http://www.ggg.ccc\',\'http://www.ggg.ccc\',309),	(41,\'http://www.ggg.ccc\',\'http://www.ggg.ccc\',\'http://www.ggg.ccc\',\'http://www.ggg.ccc\',\'http://www.ggg.ccc\',421),	(42,\'http://www.ggg.ccc\',\'http://www.ggg.ccc\',\'http://www.ggg.ccc\',\'http://www.ggg.ccc\',\'http://www.ggg.ccc\',234),	(43,\'http://www.ggg.ccc\',\'http://www.ggg.ccc\',\'http://www.ggg.ccc\',\'http://www.ggg.ccc\',\'http://www.ggg.ccc\',186);');
        $this->addSql('INSERT INTO `blog_article` (`id`, `user_id`, `handle`, `date_written`, `date_to_publish`, `title`, `content`) VALUES	(15,NULL,\'Article-1\',\'2015-12-21 16:07:20\',\'2016-05-01 04:06:00\',\'Article #1\',\'<h1>Hello</h1>\'),	(16,NULL,\'Article-2\',\'2015-12-23 11:01:02\',\'2015-01-01 00:00:00\',\'Article #2\',\'Content\'),	(18,NULL,\'Article-3\',\'2016-01-16 11:23:05\',\'2016-01-01 00:00:00\',\'Article #3\',\'<p>Content</p>\');');
        $this->addSql('INSERT INTO `billing_plan` (`id`, `necktie_id`, `amember_id`, `initial_price`, `rebill_price`, `frequency`, `rebill_times`, `price`) VALUES (3,NULL,NULL,100,0,0,0,\'$100\'),(4,NULL,NULL,30,0,0,0,\'$100\');');
        $this->addSql('INSERT INTO `product` (`id`, `handle`, `image`, `enabled`, `order_number`, `name`, `description`, `type`) VALUES (1,\'workout-with-jim\',\'http://ddd.com\',1,0,\'Workout with Jim\',\'desc\',\'standardproduct\'),(14,\'platinum-workout-with-jim\',NULL,1,0,\'Platinum Workout with Jim\',NULL,\'standardproduct\');');
        $this->addSql('INSERT INTO `product_standard` (`id`, `necktie_id`, `amember_id`, `billing_plan_id`, `notification_status`) VALUES (1,NULL,NULL,3,NULL),	(14,NULL,NULL,4,NULL);');
        $this->addSql('INSERT INTO `product_access` (`id`, `user_id`, `product_id`, `necktie_id`, `from_date`, `to_date`) VALUES (1,1,1,16,\'2016-01-16 19:39:10\',\'2016-02-15 00:00:00\'),	(5,1,14,NULL,\'2016-01-16 19:39:10\',NULL);');
        $this->addSql('INSERT INTO `contents_products` (`id`, `content_id`, `product_id`, `delay`, `order_number`) VALUES (23,39,1,0,0),(24,40,1,0,1),(25,44,1,0,2),(26,41,1,0,3),(27,42,1,0,4),(28,45,1,0,5),(29,43,1,0,6),(30,46,1,0,1),(32,47,1,0,10),(33,48,1,0,0),(34,48,14,0,0),(35,44,14,0,1),(36,39,14,0,0),(37,40,14,24,0),(38,46,14,24,1),(39,41,14,48,0),(40,42,14,72,0),(41,43,14,96,0),(42,47,14,96,1),(43,45,14,96,3);');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
