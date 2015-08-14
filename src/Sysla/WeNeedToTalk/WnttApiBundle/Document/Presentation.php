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
 *     name = "stand",
 *     href = "expr('/api/v1/stands/' ~ object.getStand().getId())",
 *     attributes = {
 *         "id" = "expr(object.getStand().getId())",
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
     */
    protected $description;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Sysla\WeNeedToTalk\WnttApiBundle\Document\Company", cascade={"remove"})
     * @Assert\NotBlank()
     * @Serializer\Expose
     * @Serializer\Groups({"inclCompany"})
     */
    protected $company;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Sysla\WeNeedToTalk\WnttApiBundle\Document\Stand", cascade={"remove"})
     * @Assert\NotBlank()
     * @Serializer\Expose
     * @Serializer\Groups({"inclStand"})
     */
    protected $stand;

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
     * @return \Sysla\WeNeedToTalk\WnttApiBundle\Document\Stand
     */
    public function getStand()
    {
        return $this->stand;
    }

    /**
     * @param \Sysla\WeNeedToTalk\WnttApiBundle\Document\Stand $stand
     */
    public function setStand($stand)
    {
        $this->stand = $stand;
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
        return $this->getVideoUrl();
    }
}