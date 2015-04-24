<?php

namespace Sysla\WeNeedToTalk\WnttApiBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(collection="stands")
 */
class Stand
{

    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\String
     */
    protected $number;

    /**
     * @MongoDB\String
     */
    protected $hall;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Sysla\WeNeedToTalk\WnttApiBundle\Document\Event", cascade={"remove"})
     */
    protected $event;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Sysla\WeNeedToTalk\WnttApiBundle\Document\Company")
     */
    protected $company;

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

    public function getClassName()
    {
        $classCanonicalName = explode('\\', get_class($this));
        return end($classCanonicalName);
    }

}
