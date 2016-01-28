<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 02.10.15
 * Time: 17:39
 */

namespace AppBundle\Entity\Content;


use AppBundle\Entity\ContentProduct;
use AppBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OrderBy;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class AbstractContent
 *
 * @ORM\Table(name="content")
 * @ORM\Entity()
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 *
 * @UniqueEntity("name")
 *
 * @package AppBundle\Entity\Content
 */
abstract class Content
{
    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     */
    protected $id;


    /**
     * @var string
     *
     * @Assert\Length(min = 3)
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false, unique=true)
     */
    protected $name;


    /**
     * @var ArrayCollection<ContentProduct>
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ContentProduct", mappedBy="content", cascade={"PERSIST", "REMOVE"})
     * @OrderBy({"orderNumber" = "ASC"})
     *
     */
    protected $contentProducts;


    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     */
    protected $author;


    /**
     * @var ContentInGroup
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Content\ContentInGroup", mappedBy="content", cascade={"PERSIST", "REMOVE"})
     * @OrderBy({"orderNumber" = "ASC"})
     */
    protected $contentsInGroup;


    /**
     * Return Content's content no matter what concrete implementation is.
     *
     * @return string
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


    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @return User
     */
    public function getAuthor()
    {
        return $this->author;
    }


    /**
     * @param User $author
     *
     * @return Content
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }


    /**
     * @return ArrayCollection<ContentProduct>
     */
    public function getContentProducts()
    {
        return $this->contentProducts;
    }


    /**
     * @param ContentProduct $contentProduct
     * @return $this
     */
    public function addContentProduct(ContentProduct $contentProduct)
    {
        if(!$this->contentProducts->contains($contentProduct))
        {
            $this->contentProducts->add($contentProduct);
        }

        return $this;
    }


    /**
     * @param ContentProduct $contentProduct
     * @return $this
     */
    public function removeContentProduct(ContentProduct $contentProduct)
    {
        $this->contentProducts->remove($contentProduct);

        return $this;
    }


    /**
     * Creates new instance of content from type (first part of entity name ends with Content)
     *
     * @param string $type Could be formatted like HtmlContent, Mp3Content, AppBundle\\Entity\\Content\\PdfContent, ...
     * @param array  $args
     *
     * @return Content
     */
    public static function createContentByType($type, $args = [])
    {
        $type = ucfirst($type);

        if(!strpos($type,"Content"))
            $type .= "Content";

        if(!strpos($type,"AppBundle\\Entity\\Content\\"))
            $type = "AppBundle\\Entity\\Content\\" . $type;

        $class = new \ReflectionClass($type);

        return $class->newInstanceArgs($args);
    }


    /**
     * Get the content type string
     *
     * @return string
     */
    abstract public function getType();


    /**
     * Get the fully classified class name of the type
     *
     * @return string
     */
    public function getFormTypeClass()
    {
        $name = get_class($this) . "Type";
        $name = str_replace('AppBundle', 'AdminBundle', $name);
        $name = str_replace('Entity', 'Form', $name);

        return $name;
    }


}