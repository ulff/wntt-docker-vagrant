<?php

namespace Sysla\WeNeedToTalk\WnttApiBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @MongoDB\Document(
 *      collection="presentations",
 *      repositoryClass="Sysla\WeNeedToTalk\WnttApiBundle\Repository\PresentationRepository"
 * )
 * @Hateoas\Relation(
 *     name = "company",
 *     href = "expr('/api/v1/companies/' ~ object.getCompany().getId())",
 *     attributes = {
 *         "id" = "expr(object.getCompany().getId())",
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
 *     "self",
 *      href = "expr('/api/v1/presentations/' ~ object.getId())"
 * )
 */
class Presentation implements Document
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\String
     * @Assert\NotBlank()
     */
    protected $videoUrl;

    /**
     * @MongoDB\String
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @MongoDB\String
     */
    protected $description;

    /**
     * @MongoDB\String
     */
    protected $number;

    /**
     * @MongoDB\String
     */
    protected $hall;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Sysla\WeNeedToTalk\WnttApiBundle\Document\Company")
     * @Assert\NotBlank()
     * @Serializer\Expose
     * @Serializer\Groups({"inclCompany"})
     */
    protected $company;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Sysla\WeNeedToTalk\WnttApiBundle\Document\Event")
     * @Assert\NotBlank()
     * @Serializer\Expose
     * @Serializer\Groups({"inclEvent"})
     */
    protected $event;

    /**
     * @MongoDB\ReferenceMany(targetDocument="Sysla\WeNeedToTalk\WnttApiBundle\Document\Category")
     */
    protected $categories;

    /**
     * @MongoDB\ReferenceMany(targetDocument="Sysla\WeNeedToTalk\WnttApiBundle\Document\Appointment", mappedBy="presentation", cascade={"remove"})
     * @Serializer\Exclude
     */
    protected $appointments;

    /**
     * @MongoDB\Boolean
     */
    protected $isPremium = false;

    /**
     * @MongoDB\String
     * @Serializer\Expose
     */
    protected $companyName;

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
    public function getVideoUrl()
    {
        return $this->videoUrl;
    }

    /**
     * @param string $videoUrl
     */
    public function setVideoUrl($videoUrl)
    {
        $this->videoUrl = $videoUrl;
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
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
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
     * @return \Sysla\WeNeedToTalk\WnttApiBundle\Document\Category[]
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param \Sysla\WeNeedToTalk\WnttApiBundle\Document\Category[] $categories
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
    }

    /**
     * @return boolean
     */
    public function getIsPremium()
    {
        return $this->isPremium;
    }

    /**
     * @param boolean $isPremium
     */
    public function setIsPremium($isPremium)
    {
        $this->isPremium = $isPremium;
    }

    /**
     * @return string
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * @param string $companyName
     */
    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;
    }

    /**
     * @param \Sysla\WeNeedToTalk\WnttApiBundle\Document\Category $category
     */
    public function removeCategory(Category $category)
    {
        /** @var $categories \Doctrine\ODM\MongoDB\PersistentCollection */
        $categories = $this->getCategories();
        $categories->removeElement($category);
        $this->setCategories($categories);
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
        return $this->getName();
    }
}
