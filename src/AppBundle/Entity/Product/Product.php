<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 03.10.15
 * Time: 13:28
 */

namespace AppBundle\Entity\Product;


use AppBundle\Entity\ProductAccess;
use AppBundle\Entity\User;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Trinity\FrameworkBundle\Entity\BaseProduct as TrinityProduct;

/**
 * Class BaseProduct
 *
 * @ORM\Table(name="product")
 * @ORM\Entity()
 *
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 *
 * @package AppBundle\Entity\Product
 */
abstract class Product extends TrinityProduct
{
    /**
     * @ORM\Column(name="handle", type="string", unique=true)
     * @var
     */
    protected $handle;


    /**
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
     *
     * @var
     */
    protected $image;


    /**
     * @var
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    protected $enabled;


    /**
     * @var integer
     *
     * @ORM\Column(name="order_number", type="integer")
     */
    protected $orderNumber;


    /**
     * @var
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ProductAccess", mappedBy="product", cascade={"remove"})
     */
    protected $productAccesses;


    /**
     * @var
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ContentProduct", mappedBy="product", cascade={"remove"})
     */
    protected $contentProducts;


    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->enabled = true;
        $this->productAccesses = new ArrayCollection();
        $this->orderNumber = 0;
    }


    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }


    /**
     * @param string $image
     *
     * @return $this
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }


    /**
     * @return string
     */
    public function getHandle()
    {
        return $this->handle;
    }


    /**
     * @param string $handle
     */
    public function setHandle($handle)
    {
        $this->handle = $handle;
    }


    /**
     * Create a new handle from given source and set it to the entity.
     *
     * @param $source String which will be source of a new handle.
     */
    public function createHandle($source)
    {
        $this->handle = (new Slugify())->slugify($source);
    }


    public function setName($name)
    {
        $this->name = $name;
        $this->createHandle($name);
    }


    /**
     * @return ArrayCollection<User>
     */
    public function getUsers()
    {
        return $this->users;
    }


    /**
     * @param User $user
     * @return $this
     */
    public function addUser(User $user)
    {
        if(!$this->users->contains($user))
        {
            $this->users->add($user);
        }

        return $this;
    }


    /**
     * @param User $user
     * @return $this
     */
    public function removeUser(User $user)
    {
        $this->users->remove($user);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEnabled()
    {
        return $this->enabled;
    }


    /**
     * @param mixed $enabled
     *
     * @return Product
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return int
     */
    public function getOrderNumber()
    {
        return $this->orderNumber;
    }


    /**
     * @param int $orderNumber
     *
     * @return Product
     */
    public function setOrderNumber($orderNumber)
    {
        $this->orderNumber = $orderNumber;

        return $this;
    }


    /**
     * @return ArrayCollection<ProductAccess>
     */
    public function getProductAccesss()
    {
        return $this->productAccesses;
    }


    /**
     * @param ProductAccess $productAccess
     * @return $this
     */
    public function addProductAccess(ProductAccess $productAccess)
    {
        if(!$this->productAccesses->contains($productAccess))
        {
            $this->productAccesses->add($productAccess);
        }

        return $this;
    }


    /**
     * @param ProductAccess $productAccess
     * @return $this
     */
    public function removeProductAccess(ProductAccess $productAccess)
    {
        $this->productAccesses->remove($productAccess);

        return $this;
    }

    /**
     * Creates new instance of product from type (first part of entity name ends with Product)
     *
     * @param string $type Could be formatted like StandardProduct, FreeProduct, AppBundle\\Entity\\Product\\StandardProduct, ...
     * @param array  $args
     *
     * @return Product
     */
    public static function createProductByType($type, $args = [])
    {
        $type = ucfirst($type);

        if(!strpos($type,"Product"))
            $type .= "Product";

        if(!strpos($type,"AppBundle\\Entity\\Product\\"))
            $type = "AppBundle\\Entity\\Product\\" . $type;

        $class = new \ReflectionClass($type);

        return $class->newInstanceArgs($args);
    }


    /**
     * Get the product type string
     *
     * @return string
     */
    abstract public function getType();


    /**
     * Get form type of product
     *
     * @param array|null $arguments
     *
     * @return StandardProduct|FreeProduct
     */
    public function getFormType($arguments = [])
    {
        $name = get_class($this) . "Type";
        $name = str_replace('AppBundle', 'AdminBundle', $name);
        $name = str_replace('Entity', 'Form', $name);

        $class = new \ReflectionClass($name);
        return $class->newInstanceArgs($arguments);
    }

}