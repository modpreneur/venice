<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 04.10.15
 * Time: 14:49
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class ContentProduct
 *
 * @ORM\Entity()
 * @ORM\Table(name="contents_products")
 *
 * @package AppBundle\Entity
 */
class ContentProduct
{
    /**
     * @var
     *
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Content\Content", inversedBy="contentProducts")
     * @ORM\JoinColumn(name="content_id", referencedColumnName="id")
     */
    protected $content;

    /**
     * @var
     *
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Product\Product", inversedBy="contentProducts")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    protected $product;

    /**
     * @var
     *
     * @ORM\Column(name="delay", type="integer", nullable=false)
     */
    protected $delay;

    /**
     * @var
     *
     * @ORM\Column(name="order_number", type="integer", nullable=false)
     */
    protected $orderNumber;
}