<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 31.12.15
 * Time: 10:41.
 */
namespace Venice\AppBundle\Twig;

/**
 * Class HumanBoolExtension.
 */
class HumanBoolExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('humanBool', [$this, 'boolFilter']),
        ];
    }

    public function boolFilter($bool)
    {
        return $bool ? 'yes' : 'no';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'venice_app_bundle_human_bool_twig_extension';
    }
}
