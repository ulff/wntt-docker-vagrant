<?php

namespace Sysla\WeNeedToTalk\WnttApiBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;
use Symfony\Component\HttpFoundation\Request;

/**
 * @MongoDB\Document(collection="stands")
 * @Hateoas\Relation(
 *     name = "event",
 *     href = "expr('/api/v1/events/' ~ object.getEvent().getId())",
 *     attributes = {
 *         "id" = "expr(object.getEvent().getId())",
 *     },
 * )
 * @Hateoas\Relation(
 *     name = "company",
 *     href = "expr('/api/v1/companies/' ~ object.getCompany().getId())",
 *     attributes = {
 *         "id" = "expr(object.getCompany().getId())",
 *     },
 *     exclusion = @Hateoas\Exclusion(excludeIf = "expr(null === object.getCompany())")
 * )
 * @Hateoas\Relation(
 *     "self",
 *      href = "expr('/api/v1/stands/' ~ object.getId())"
 * )
 */
class Stand implements Document
{

    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\String
     * @Assert\NotBlank()
     */
    protected $number;

    /**
     * @MongoDB\String
     */
    protected $hall;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Sysla\WeNeedToTalk\WnttApiBundle\Document\Event", inversedBy="stands")
     * @Assert\NotBlank()
     * @Serializer\Exclude
     */
    protected $event;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Sysla\WeNeedToTalk\WnttApiBundle\Document\Company", inversedBy="stands")
     * @Serializer\Exclude
     */
    protected $company;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Sysla\WeNeedToTalk\WnttApiBundle\Document\Presentation", mappedBy="stand", cascade={"remove"})
     */
    protected $presentation;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param string $number
     */
    public function setNumber($number)
    {
        $this->number = $number;
    }

    /**
     * @return string
     */
    public function getHall()
    {
        return $this->hall;
    }

    /**
     * @param string $hall
     */
    public function setHall($hall)
    {
        $this->hall = $hall;
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
     * @return \Sysla\WeNeedToTalk\WnttApiBundle\Document\Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param \Sysla\WeNeedToTalk\WnttApiBundle\Document\Company $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     * @return \Sysla\WeNeedToTalk\WnttApiBundle\Document\Presentation
     */
    public function getPresentation()
    {
        return $this->presentation;
    }

    /**
     * @return boolean
     */
    public function hasEvent()
    {
        $event = $this->getEvent();
        return !empty($event);
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        $classCanonicalName = explode('\\', get_class($this));
        return end($classCanonicalName);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->hasEvent() ? $this->getNumber().' ('.$this->getEvent()->getName().')' : '';
    }

}
