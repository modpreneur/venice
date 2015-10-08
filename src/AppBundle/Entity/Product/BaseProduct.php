<?php
/**
 * Created by PhpStorm.
 * User: Jakub Fajkus
 * Date: 03.10.15
 * Time: 13:28
 */

namespace AppBundle\Entity\Product;


use AppBundle\Entity\User;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Trinity\FrameworkBundle\Entity\BaseProduct as TrinityProduct;

/**
 * Class BaseProduct
 *
 * @ORM\Table(name="base_product")
 * @ORM\Entity()
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 *
 * @package AppBundle\Entity\Product
 */
abstract class BaseProduct extends TrinityProduct
{
    /**
     * @ORM\Column(name="handle", type="string", unique=true)
     * @var
     */
    protected $handle;


    /**
     * @var ArrayCollection<User>
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinTable(name="base_products_users")
     */
    protected $users;


    /**
     * @var ArrayCollection<Tag>
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Product\Tag")
     * @ORM\JoinTable(name="base_products_tags")
     */
    protected $tags;

    /**
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
     *
     * @var
     */
    protected $image;


    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->enabled = true;
    }


    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }


    /**
     * @param string $image
     *
     * @return $this
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }


    /**
     * @return string
     */
    public function getHandle()
    {
        return $this->handle;
    }


    /**
     * @param string $handle
     */
    public function setHandle($handle)
    {
        $this->handle = $handle;
    }


    /**
     * Create a new handle from given source and set it to the entity.
     *
     * @param $source String which will be source of a new handle.
     */
    public function createHandle($source)
    {
        $this->handle = (new Slugify())->slugify($source);
    }


    public function setName($name)
    {
        $this->name = $name;
        $this->createHandle($name);
    }

    /**
     * @return ArrayCollection<Tag>
     */
    public function getTags()
    {
        return $this->tags;
    }


    /**
     * @param Tag $tag
     * @return $this
     */
    public function addTag(Tag $tag)
    {
        if(!$this->tags->contains($tag))
        {
            $this->tags->add($tag);
        }

        return $this;
    }


    /**
     * @param Tag $tag
     * @return $this
     */
    public function removeTag(Tag $tag)
    {
        $this->tags->remove($tag);

        return $this;
    }


    /**
     * @return ArrayCollection<User>
     */
    public function getUsers()
    {
        return $this->users;
    }


    /**
     * @param User $user
     * @return $this
     */
    public function addUser(User $user)
    {
        if(!$this->users->contains($user))
        {
            $this->users->add($user);
        }

        return $this;
    }


    /**
     * @param User $user
     * @return $this
     */
    public function removeUser(User $user)
    {
        $this->users->remove($user);

        return $this;
    }

}