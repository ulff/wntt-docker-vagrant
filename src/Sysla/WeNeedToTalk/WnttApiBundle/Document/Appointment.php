<?php

namespace Sysla\WeNeedToTalk\WnttApiBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @MongoDB\Document(collection="appointments")
 * @Hateoas\Relation(
 *     name = "user",
 *     href = "expr('/api/v1/users/' ~ object.getUser().getId())",
 *     attributes = {
 *         "id" = "expr(object.getUser().getId())",
 *     },
 * )
 * @Hateoas\Relation(
 *     name = "event",
 *     href = "expr('/api/v1/events/' ~ object.getEvent().getId())",
 *     attributes = {
 *         "id" = "expr(object.getEvent().getId())",
 *     },
 * )
 * @Hateoas\Relation(
 *     name = "presentation",
 *     href = "expr('/api/v1/presentations/' ~ object.getPresentation().getId())",
 *     attributes = {
 *         "id" = "expr(object.getPresentation().getId())",
 *     },
 * )
 * @Hateoas\Relation(
 *     "self",
 *      href = "expr('/api/v1/appointments/' ~ object.getId())"
 * )
 */
class Appointment implements Document
{

    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Sysla\WeNeedToTalk\WnttUserBundle\Document\User", inversedBy="appointments")
     * @Assert\NotBlank()
     * @Serializer\Exclude
     */
    protected $user;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Sysla\WeNeedToTalk\WnttApiBundle\Document\Event", inversedBy="appointments")
     * @Assert\NotBlank()
     * @Serializer\Exclude
     */
    protected $event;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Sysla\WeNeedToTalk\WnttApiBundle\Document\Presentation", inversedBy="appointments")
     * @Serializer\Expose
     * @Serializer\Groups({"inclPresentation"})
     */
    protected $presentation;

    /**
     * @MongoDB\Boolean
     */
    protected $isVisited;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return \Sysla\WeNeedToTalk\WnttUserBundle\Document\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param \Sysla\WeNeedToTalk\WnttUserBundle\Document\User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return \Sysla\WeNeedToTalk\WnttApiBundle\Document\Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param \Sysla\WeNeedToTalk\WnttApiBundle\Document\Event $event
     */
    public function setEvent($event)
    {
        $this->event = $event;
    }

    /**
     * @return \Sysla\WeNeedToTalk\WnttApiBundle\Document\Presentation
     */
    public function getPresentation()
    {
        return $this->presentation;
    }

    /**
     * @param \Sysla\WeNeedToTalk\WnttApiBundle\Document\Presentation $presentation
     */
    public function setPresentation($presentation)
    {
        $this->presentation = $presentation;
    }

    /**
     * @return boolean
     */
    public function getIsVisited()
    {
        return $this->isVisited;
    }

    /**
     * @param boolean $isVisited
     */
    public function setIsVisited($isVisited)
    {
        $this->isVisited = $isVisited;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        $classCanonicalName = explode('\\', get_class($this));
        return end($classCanonicalName);
    }
}
