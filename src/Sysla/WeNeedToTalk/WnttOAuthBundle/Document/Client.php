<?php

namespace Sysla\WeNeedToTalk\WnttOAuthBundle\Document;

use FOS\OAuthServerBundle\Document\Client as BaseClient;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(collection="oauth_clients")
 */
class Client extends BaseClient
{
    /**
     * @MongoDB\Id(strategy="auto")
     */
    protected $id;

    /**
     * @MongoDB\String
     */
    protected $name;

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
}
