<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 31.12.15
 * Time: 10:41
 */

namespace AppBundle\Twig;

class HumanBoolExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('humanBool', array($this, 'boolFilter')),
        );
    }

    public function boolFilter($bool)
    {
        return $bool? "yes" : "no";
    }

    public function getName()
    {
        return 'venice_app_bundle_human_bool_twig_extension';
    }
}