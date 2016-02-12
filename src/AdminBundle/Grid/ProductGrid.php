<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 31.01.16
 * Time: 10:50
 */

namespace AdminBundle\Grid;


use Trinity\Bundle\GridBundle\Grid\BaseGrid;

class ProductGrid extends BaseGrid
{

    /**
     * Set up grid (template)
     *
     * @return void
     */
    protected function setUp()
    {
        $this->addTemplate("AdminBundle:Product:grid.html.twig");
    }
}