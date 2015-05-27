<?php

namespace Sysla\WeNeedToTalk\WnttUserBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique as MongoDBUnique;
use FOS\UserBundle\Model\User as BaseUser;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;
use Sysla\WeNeedToTalk\WnttApiBundle\Document\Document;

/**
 * @MongoDB\Document(collection="users")
 * @MongoDBUnique(fields="username")
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
 *      href = "expr('/api/v1/users/' ~ object.getId())"
 * )
 */
class User extends BaseUser implements Document
{
    const ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * @MongoDB\Id(strategy="auto")
     */
    protected $id;

    /**
     * @MongoDB\String
     */
    protected $phoneNumber;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Sysla\WeNeedToTalk\WnttApiBundle\Document\Company", cascade={"remove"})
     * @Serializer\Exclude
     */
    protected $company;

    /**
     * @MongoDB\Boolean
     */
    protected $isContactPerson;

    public function __construct()
    {
        parent::__construct();
        $this->setEnabled(true);
    }

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
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * @param string $phoneNumber
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
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
     * @return boolean
     */
    public function getIsContactPerson()
    {
        return $this->isContactPerson;
    }

    /**
     * @param boolean $isContactPerson
     */
    public function setIsContactPerson($isContactPerson)
    {
        $this->isContactPerson = $isContactPerson;
    }

    public function getClassName()
    {
        $classCanonicalName = explode('\\', get_class($this));
        return end($classCanonicalName);
    }

    public function hasAdminRights()
    {
        return $this->hasRole(static::ROLE_SUPER_ADMIN) || $this->hasRole(static::ROLE_ADMIN);
    }

}