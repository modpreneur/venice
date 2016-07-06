<?php


namespace Venice\AdminBundle\Grid;


use Trinity\Bundle\GridBundle\Grid\BaseGrid;
use Trinity\Bundle\SearchBundle\NQL\NQLQuery;

/**
 * Class UserGrid
 * @package Venice\AdminBundle\Grid
 */
class UserGrid extends BaseGrid
{

    /**
     * Set up grid (template)
     *
     * @return void
     */
    public function setUp()
    {
        $this->addTemplate("VeniceAdminBundle:User:grid.html.twig");
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