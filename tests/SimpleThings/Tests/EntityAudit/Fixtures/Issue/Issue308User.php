<?php

namespace SimpleThings\EntityAudit\Tests\Fixtures\Issue;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Issue308User
{
    /** @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue(strategy="AUTO") */
    private $id;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Issue308User", mappedBy="parent")
     */
    private $children;

    /**
     * @var Issue308User
     *
     * @ORM\ManyToOne(targetEntity="Issue308User", inversedBy="children")
     */
    private $parent;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @transient
     *
     * @return bool
     */
    public function isActive()
    {
        return false;
    }

    /**
     * @param Issue308User $child
     */
    public function addChild(Issue308User $child)
    {
        $this->children->add($child);
    }

    /**
     * @return ArrayCollection
     */
    public function getChildren()
    {
        $activeChildren = $this->children->filter(function (Issue308User $user) {
            return $user->isActive();
        });

        return $activeChildren;
    }

    /**
     * @return Issue308User
     */
    public function getParent()
    {
        return $this->parent;
    }
}
