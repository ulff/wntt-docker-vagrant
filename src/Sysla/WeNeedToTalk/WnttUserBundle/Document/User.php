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
     * @MongoDB\String
     */
    protected $fullName;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Sysla\WeNeedToTalk\WnttApiBundle\Document\Company")
     * @Serializer\Exclude
     */
    protected $company;

    /**
     * @MongoDB\ReferenceMany(targetDocument="Sysla\WeNeedToTalk\WnttApiBundle\Document\Appointment", mappedBy="user", cascade={"remove"})
     * @Serializer\Exclude
     */
    protected $appointments;

    /**
     * @MongoDB\Boolean
     */
    protected $isContactPerson;

    /**
     * @MongoDB\String
     */
    protected $defaultPassword;

    /**
     * @MongoDB\Boolean
     */
    protected $isDefaultPassword;

    public function __construct()
    {
        parent::__construct();
        $this->setEnabled(true);
        $this->setIsDefaultPassword(false);
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
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * @param string $fullName
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;
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

    /**
     * @return string
     */
    public function getDefaultPassword()
    {
        return $this->defaultPassword;
    }

    /**
     * @param string $defaultPassword
     */
    public function setDefaultPassword($defaultPassword)
    {
        $this->defaultPassword = $defaultPassword;
    }

    /**
     * @return boolean
     */
    public function getIsDefaultPassword()
    {
        return $this->isDefaultPassword;
    }

    /**
     * @param boolean $isDefaultPassword
     */
    public function setIsDefaultPassword($isDefaultPassword)
    {
        $this->isDefaultPassword = $isDefaultPassword;
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