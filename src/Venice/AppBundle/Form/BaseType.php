<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 03.11.15
 * Time: 18:12
 */

namespace Venice\AppBundle\Form;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;

class BaseType extends AbstractType
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
}