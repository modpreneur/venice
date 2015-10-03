<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 02.10.15
 * Time: 17:54
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;

/**
 * Class Immersion
 *
 * @Entity()
 * @Table(name="immersion")
 *
 * @package AppBundle\Entity
 */
class Immersion
{
    protected $id;

    protected $user;

    protected $product;

    protected $start;

    protected $end;
}