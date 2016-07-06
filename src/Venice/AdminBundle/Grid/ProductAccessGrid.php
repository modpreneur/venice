<?php

namespace Venice\AdminBundle\Grid;

use Trinity\Bundle\GridBundle\Grid\BaseGrid;

/**
 * Class productAccessGrid
 * @package Venice\AdminBundle\Grid
 */
class ProductAccessGrid extends BaseGrid
{
    /**
     * Set up grid (template)
     *
     * @return void
     */
    public function setUp()
    {
        $this->addTemplate('VeniceAdminBundle:ProductAccess:grid.html.twig');
    }
}