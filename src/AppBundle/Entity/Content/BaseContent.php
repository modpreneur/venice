<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 02.10.15
 * Time: 17:39
 */

namespace AppBundle\Entity\Content;


use Doctrine\ORM\Mapping as ORM;

/**
 * Class AbstractContent
 *
 * @ORM\Table(name="content")
 * @ORM\Entity()
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 *
 * @package AppBundle\Entity\Content
 */
abstract class BaseContent
{
    /**
     * @var
     *
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     */
    protected $id;

    /**
     * @var
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ContentProduct", mappedBy="content")
     */
    protected $contentProducts;


    /**
     * Return Content's content no matter what concrete implementation is.
     *
     * @return string
     *
     */
    abstract public function getContent();


    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}