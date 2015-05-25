<?php

namespace Sysla\WeNeedToTalk\WnttApiBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;
use Hateoas\Configuration\Annotation as Hateoas;


/**
 * @MongoDB\Document(collection="companies")
 * @Hateoas\Relation(
 *     "self",
 *      href = "expr('/api/v1/companies/' ~ object.getId())"
 * )
 */
class Company implements Document
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
    protected $websiteUrl;

    /**
     * @MongoDB\String
     */
    protected $logoUrl;

    /**
     * @MongoDB\ReferenceMany(targetDocument="Sysla\WeNeedToTalk\WnttApiBundle\Document\Stand", mappedBy="company", cascade={"remove"})
     */
    protected $stands;

    /**
     * @MongoDB\ReferenceMany(targetDocument="Sysla\WeNeedToTalk\WnttApiBundle\Document\Presentation", mappedBy="company", cascade={"remove"})
     */
    protected $presentations;

    /**
     * @MongoDB\ReferenceMany(targetDocument="Sysla\WeNeedToTalk\WnttUserBundle\Document\User", mappedBy="company", cascade={"remove"})
     */
    protected $users;

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
    public function getWebsiteUrl()
    {
        return $this->websiteUrl;
    }

    /**
     * @param string $websiteUrl
     */
    public function setWebsiteUrl($websiteUrl)
    {
        $this->websiteUrl = $websiteUrl;
    }

    /**
     * @return string
     */
    public function getLogoUrl()
    {
        return $this->logoUrl;
    }

    /**
     * @param string $logoUrl
     */
    public function setLogoUrl($logoUrl)
    {
        $this->logoUrl = $logoUrl;
    }

    public function getClassName()
    {
        $classCanonicalName = explode('\\', get_class($this));
        return end($classCanonicalName);
    }

    public function __toString()
    {
        return $this->getName();
    }
}
