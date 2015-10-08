<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 02.10.15
 * Time: 17:45
 */

namespace AppBundle\Entity\Product;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

class ProductBundle extends Product
{
    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Product\Product")
     *
     * @var
     */
    protected $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    /**
     * @return ArrayCollection<Product>
     */
    public function getProducts()
    {
        return $this->products;
    }


    /**
     * @param Product $product
     * @return $this
     */
    public function addProduct(Product $product)
    {
        if(!$this->products->contains($product))
        {
            $this->products->add($product);
        }

        return $this;
    }


    /**
     * @param Product $product
     * @return $this
     */
    public function removeProduct(Product $product)
    {
        $this->products->remove($product);

        return $this;
    }


}