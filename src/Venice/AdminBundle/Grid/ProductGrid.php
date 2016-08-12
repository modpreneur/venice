<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 31.01.16
 * Time: 10:50
 */

namespace Venice\AdminBundle\Grid;

use Trinity\Bundle\GridBundle\Grid\BaseGrid;

/**
 * Class ProductGrid
 *
 * @package Venice\AdminBundle\Grid
 */
class ProductGrid extends BaseGrid
{
    /**
     * Set up grid (template)
     *
     * @return void
     */
    public function setUp()
    {
        $this->addTemplate('VeniceAdminBundle:Product:grid.html.twig');
    }
}