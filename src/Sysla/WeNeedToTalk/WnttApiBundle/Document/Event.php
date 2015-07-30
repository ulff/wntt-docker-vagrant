<?php

namespace Sysla\WeNeedToTalk\WnttApiBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @MongoDB\Document(collection="events")
 * @Hateoas\Relation(
 *     "self",
 *      href = "expr('/api/v1/events/' ~ object.getId())"
 * )
 *
 */
class Event implements Document
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\String
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @MongoDB\String
     */
    protected $location;

    /**
     * @MongoDB\Date
     * @Assert\NotBlank()
     */
    protected $dateStart;

    /**
     * @MongoDB\Date
     * @Assert\NotBlank()
     */
    protected $dateEnd;

    /**
     * @MongoDB\ReferenceMany(targetDocument="Sysla\WeNeedToTalk\WnttApiBundle\Document\Stand", mappedBy="event", cascade={"remove"})
     * @Serializer\Exclude
     */
    protected $stands;

    /**
     * @MongoDB\ReferenceMany(targetDocument="Sysla\WeNeedToTalk\WnttApiBundle\Document\Appointment", mappedBy="event", cascade={"remove"})
     * @Serializer\Exclude
     */
    protected $appointments;

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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param string $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return \DateTime
     */
    public function getDateStart()
    {
        return $this->dateStart;
    }

    /**
     * return string
     */
    public function getDateStartAsString($format = 'Y-m-d')
    {
        $dateStart = $this->getDateStart();
        return empty($dateStart) ? '' : $dateStart->format($format);
    }

    /**
     * @param \DateTime $dateStart
     */
    public function setDateStart($dateStart)
    {
        $this->dateStart = $dateStart;
    }

    /**
     * @return \DateTime
     */
    public function getDateEnd()
    {
        return $this->dateEnd;
    }

    /**
     * return string
     */
    public function getDateEndAsString($format = 'Y-m-d')
    {
        $dateEnd = $this->getDateEnd();
        return empty($dateEnd) ? '' : $dateEnd->format($format);
    }

    /**
     * @param \DateTime $dateEnd
     */
    public function setDateEnd($dateEnd)
    {
        $this->dateEnd = $dateEnd;
    }

    public function getClassName()
    {
        $classCanonicalName = explode('\\', get_class($this));
        return end($classCanonicalName);
    }

    public function __toString()
    {
        return $this->getName().' ('.$this->getDateStartAsString().' - '.$this->getDateEndAsString().')';
    }

}
