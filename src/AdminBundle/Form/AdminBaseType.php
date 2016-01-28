<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 03.11.15
 * Time: 18:13
 */

namespace AdminBundle\Form;


use AppBundle\Form\BaseType;
use Doctrine\ORM\EntityManagerInterface;

class AdminBaseType extends BaseType
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

}