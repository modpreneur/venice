<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 31.01.16
 * Time: 10:50.
 */
namespace Venice\AdminBundle\Grid;

/**
 * Class TagGrid.
 */
class BlogArticleGrid extends BaseVeniceGrid
{
    /**
     * Set up grid (template).
     */
    public function setUp()
    {
        $this->addTemplate('VeniceAdminBundle:BlogArticle:grid.html.twig');
    }
}
