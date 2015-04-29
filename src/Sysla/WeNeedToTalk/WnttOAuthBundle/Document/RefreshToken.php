<?php

namespace Sysla\WeNeedToTalk\WnttOAuthBundle\Document;

use FOS\OAuthServerBundle\Document\RefreshToken as BaseRefreshToken;
use FOS\OAuthServerBundle\Model\ClientInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @MongoDB\Document(collection="oauth_refresh_tokens")
 */
class RefreshToken extends BaseRefreshToken
{
    /**
     * @MongoDB\Id(strategy="auto")
     */
    protected $id;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Sysla\WeNeedToTalk\WnttOAuthBundle\Document\Client")
     */
    protected $client;

    /**
     * @MongoDB\ReferenceOne(targetDocument="\Sysla\WeNeedToTalk\WnttUserBundle\Document\User")
     */
    protected $user;

    /**
     * @return ClientInterface
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param ClientInterface $client
     */
    public function setClient(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;
    }
}
