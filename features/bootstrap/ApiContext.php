<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Symfony2Extension\Context\KernelDictionary;
use OAuth2\OAuth2;
use Codifico\ParameterBagExtension\Context\ParameterBagDictionary;

class ApiContext extends MinkContext implements Context, SnippetAcceptingContext
{
    use ParameterBagDictionary;
    use KernelDictionary;

    protected $headers = [];

    public function __construct()
    {
    }

    /**
     * @When I make request :method :uri
     */
    public function iMakeRequest($method, $uri)
    {
        $uri = '/app_dev.php'.$uri;
        $this->request($method, $uri);
    }

    /**
     * @When I make request :method :uri with params:
     */
    public function iMakeRequestWithParams($method, $uri, TableNode $table)
    {
        $uri = '/app_dev.php'.$uri;
        $this->request($method, $uri, $table->getRowsHash());
    }

    /**
     * @When I make request :method :uri with parameter-bag params:
     */
    public function iMakeRequestWithParameterBagParams($method, $uri, TableNode $table)
    {
        $uri = '/app_dev.php'.$uri;
        $params = [];
        foreach($table->getRowsHash() as $field => $value) {
            if(in_array($value, ['CLIENT_PUBLIC_ID', 'CLIENT_SECRET'])) {
                $value = $this->getParameterBag()->get($value);
            }
            $params[$field] = $value;
        }
        $this->request($method, $uri, $params);
    }

    /**
     * @Given client is created
     */
    public function clientIsCreated()
    {
        $clientManager = $this->getContainer()->get('fos_oauth_server.client_manager.default');

        $client = $clientManager->createClient();
        $client->setName('test-client-name');
        $client->setAllowedGrantTypes([
            OAuth2::GRANT_TYPE_CLIENT_CREDENTIALS
        ]);
        $clientManager->updateClient($client);

        $this->getParameterBag()->set('CLIENT_PUBLIC_ID', $client->getPublicId());
        $this->getParameterBag()->set('CLIENT_SECRET', $client->getSecret());
    }

    /**
     * @Given I am authorized client
     */
    public function iAmAuthorizedClient()
    {
        $this->clientIsCreated();
        $this->request('POST', '/app_dev.php/oauth/v2/token', [
            'client_id' => $this->getParameterBag()->get('CLIENT_PUBLIC_ID'),
            'client_secret' => $this->getParameterBag()->get('CLIENT_SECRET'),
            'response_type' => 'code',
            'grant_type' => 'client_credentials'
        ]);

        $clientManager = $this->getContainer()->get('fos_oauth_server.client_manager.default');
        $client = $clientManager->findClientByPublicId($this->getParameterBag()->get('CLIENT_PUBLIC_ID'));

        $dm = $this->getContainer()->get('doctrine_mongodb')->getManager();

        $accessToken = $dm->getRepository('SyslaWeeNeedToTalkWnttOAuthBundle:AccessToken')
            ->findOneBy(array('client.id' => $client->getId()));

        $this->getParameterBag()->set('ACCESS_TOKEN', $accessToken->getToken());
        $this->iSetHeaderWithValue('Authorization', 'Bearer ' . $this->getParameterBag()->get('ACCESS_TOKEN'));
    }

    /**
     * @Given /^I set header "([^"]*)" with value "([^"]*)"$/
     */
    public function iSetHeaderWithValue($name, $value)
    {
        $this->headers[$name] = $value;
    }

    protected function request($method, $uri, array $params = array(), array $headers = array())
    {
        $headers = array_merge($headers, $this->headers);
        $server = $this->createServerArray($headers);
        $this->getClient()->request($method, $this->locatePath($uri), $params, array(), $server);
    }

    protected function createServerArray(array $headers = array())
    {
        $server = array();
        $nonPrefixed = array('CONTENT_TYPE');
        foreach ($headers as $name => $value) {
            $headerName = strtoupper(str_replace('-', '_', $name));
            $headerName = in_array($headerName, $nonPrefixed) ? $headerName : 'HTTP_'.$headerName;
            $server[$headerName] = $value;
        }
        return $server;
    }

    protected function getClient()
    {
        $driver = $this->getSession()->getDriver();
        return $driver->getClient();
    }

}
