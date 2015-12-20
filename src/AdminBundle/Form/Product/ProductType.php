<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 03.11.15
 * Time: 18:03
 */

namespace AdminBundle\Form\Product;


use AdminBundle\Form\AdminBaseType;
use AppBundle\Entity\Product\Product;
use AppBundle\Services\CMSProblemHelper;
use Symfony\Component\Form\FormBuilderInterface;

class ProductType extends AdminBaseType
{
    protected $product;

    function __construct(Product $product = null)
    {
        $this->product = $product;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("name", "text")
            ->add("description", "textarea", ["required" => false])
            ->add("image", "text")
            ->add("enabled", "checkbox")
            ->add("orderNumber", "integer")
        ;
    }
}