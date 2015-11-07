<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 03.11.15
 * Time: 18:12
 */

namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;

class BaseType extends AbstractType
{
    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        $formName = (new \ReflectionClass($this))->getShortName();
        $formName = strtolower(str_replace("\\", "_", $formName));

        return $formName;
    }
}