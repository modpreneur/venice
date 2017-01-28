<?php
namespace Venice\AdminBundle\Grid;

/**
 * Class TagGrid.
 */
class TagGrid extends BaseVeniceGrid
{
    /**
     * Set up grid (template).
     */
    public function setUp()
    {
        $this->addTemplate('VeniceAdminBundle:Tag:grid.html.twig');
    }
}
