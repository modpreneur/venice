<?php
namespace Venice\AdminBundle\Grid;

/**
 * Class ProductGrid.
 */
class CategoryGrid extends BaseVeniceGrid
{
    /**
     * Set up grid (template).
     */
    public function setUp()
    {
        $this->addTemplate('VeniceAdminBundle:Category:grid.html.twig');
    }
}
