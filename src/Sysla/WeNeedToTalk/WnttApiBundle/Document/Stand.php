<?php

namespace Sysla\WeNeedToTalk\WnttApiBundle\Document;


class Stand
{

    protected $id;
    protected $number;

    /**
     * @return integer
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
     * @return integer
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param integer $number
     */
    public function setNumber($number)
    {
        $this->number = $number;
    }

}