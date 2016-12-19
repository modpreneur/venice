<?php

namespace Venice\AdminBundle\Grid;

use Trinity\Bundle\GridBundle\Grid\BaseGrid;
use Trinity\Bundle\SearchBundle\NQL\NQLQuery;

/**
 * Class UserGrid.
 */
class UserGrid extends BaseVeniceGrid
{
    /**
     * Set up grid (template).
     */
    public function setUp()
    {
        $this->addTemplate('VeniceAdminBundle:User:grid.html.twig');
    }

    /**
     * @param NQLQuery $query
     */
    public function prepareQuery(NQLQuery $query)
    {
        $query->getWhere()->replaceColumn('fullName', ['firstName', 'lastName']);
        $query->getOrderBy()->replaceColumn('fullName', ['firstName', 'lastName']);
    }
}
