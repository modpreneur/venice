<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 05.11.15
 * Time: 10:18
 */

namespace AdminBundle\Form\Product;


use AppBundle\Entity\Product\FreeProduct;
use AppBundle\Services\CMSProblemHelper;
use Symfony\Component\Form\FormBuilderInterface;

class FreeProductType extends ProductType
{
    function __construct(FreeProduct $product = null, CMSProblemHelper $CMSProblemHelper)
    {
        parent::__construct($product, $CMSProblemHelper);

        $this->product = $product;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add("Submit", "submit");
    }

}