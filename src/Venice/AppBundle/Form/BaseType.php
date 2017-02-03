<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 03.11.15
 * Time: 18:12.
 */
namespace Venice\AppBundle\Form;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Venice\AppBundle\Services\EntityOverrideHandler;

/**
 * Class BaseType
 */
abstract class BaseType extends AbstractType
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var  EntityOverrideHandler */
    protected $entityOverrideHandler;

    public function __construct(EntityManagerInterface $entityManager, EntityOverrideHandler $entityOverrideHandler)
    {
        $this->entityManager = $entityManager;
        $this->entityOverrideHandler = $entityOverrideHandler;
    }
}
