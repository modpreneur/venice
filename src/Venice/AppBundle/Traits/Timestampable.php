<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 16.02.16
 * Time: 14:25
 */

namespace Venice\AppBundle\Traits;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableMethods;

trait Timestampable
{
    use TimestampableMethods;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="create")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=false)
     * @Gedmo\Timestampable(on="update")
     */
    protected $updatedAt;
}
