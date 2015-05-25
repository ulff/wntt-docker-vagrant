<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Symfony2Extension\Context\KernelDictionary;
use OAuth2\OAuth2;
use Codifico\ParameterBagExtension\Context\ParameterBagDictionary;
use Sysla\WeNeedToTalk\WnttApiBundle\Document as Document;
use Sysla\WeNeedToTalk\WnttUserBundle\Document\User;
use Sysla\WeNeedToTalk\WnttApiBundle\Exception as Exception;

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
        $uri = $this->extractFromParameterBag($uri);
        $this->request($method, $uri);
    }

    /**
     * @When I make request :method :uri with params:
     */
    public function iMakeRequestWithParams($method, $uri, TableNode $table)
    {
        $uri = '/app_dev.php'.$uri;
        $uri = $this->extractFromParameterBag($uri);
        $this->request($method, $uri, $table->getRowsHash());
    }

    /**
     * @When I make request :method :uri with parameter-bag params:
     */
    public function iMakeRequestWithParameterBagParams($method, $uri, TableNode $table)
    {
        $uri = '/app_dev.php'.$uri;
        $uri = $this->extractFromParameterBag($uri);
        $params = [];
        foreach($table->getRowsHash() as $field => $value) {
            $params[$field] = $this->getParameterBag()->replace($value);;
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

    /**
     * @Given following :documentName exists:
     */
    public function followingDocumentExists($documentName, TableNode $table)
    {
        switch($documentName) {
            case 'Category':
                $this->ensureCategoryExists($table->getRowsHash());
                break;
            case 'Company':
                $this->ensureCompanyExists($table->getRowsHash());
                break;
            case 'Event':
                $this->ensureEventExists($table->getRowsHash());
                break;
            case 'Stand':
                $this->ensureStandExists($table->getRowsHash());
                break;
            case 'Presentation':
                $this->ensurePresentationExists($table->getRowsHash());
                break;
            case 'User':
                $this->ensureUserExists($table->getRowsHash());
                break;
            default:
                throw new \Exception("Class $documentName not found");
        }
    }

    /**
     * @Then the response should be JSON
     */
    public function theResponseShouldBeJson()
    {
        $response = $this->getClient()->getResponse()->getContent();
        if(json_decode($response) === null) {
            throw new Exception\JsonExpectedException();
        }
    }

    /**
     * @Then the response JSON should be a collection
     */
    public function theResponseJsonShouldBeACollection()
    {
        $response = json_decode($this->getClient()->getResponse()->getContent());
        if(!is_array($response)) {
            throw new Exception\CollectionExpectedException();
        }
        return;
    }

    /**
     * @Then the response JSON should be a single object
     */
    public function theResponseJsonShouldBeASingleObject()
    {
        $response = json_decode($this->getClient()->getResponse()->getContent());
        if(!is_object($response)) {
            throw new Exception\SingleObjectExpectedException();
        }
        return;
    }

    /**
     * @Then the repsonse JSON should have :property field
     */
    public function theRepsonseJsonShouldHaveField($property)
    {
        $response = json_decode($this->getClient()->getResponse()->getContent());
        $this->assertDocumentHasProperty($response, $property);
        return;
    }

    /**
     * @Then the repsonse JSON should have :property field with value :expectedValue
     */
    public function theRepsonseJsonShouldHaveFieldWithValue($property, $expectedValue)
    {
        $response = json_decode($this->getClient()->getResponse()->getContent());
        $this->assertDocumentHasPropertyWithValue($response, $property, $expectedValue);
        return;
    }

    /**
     * @Then all response collection items should have :property field with value :expectedValue
     */
    public function allResponseCollectionItemsShouldHaveFieldWithValue($property, $expectedValue)
    {
        $response = json_decode($this->getClient()->getResponse()->getContent());
        foreach($response as $document) {
            $this->assertDocumentHasPropertyWithValue($document, $property, $expectedValue);
        }
        return;
    }

    /**
     * @Then all response collection items should have :property field set to :expectedBoolean
     */
    public function allResponseCollectionItemsShouldHaveFieldSetTo($property, $expectedBoolean)
    {
        $response = json_decode($this->getClient()->getResponse()->getContent());
        foreach($response as $document) {
            $this->assertDocumentHasPropertyWithBooleanValue($document, $property, $expectedBoolean);
        }
        return;
    }

    /**
     * @Then :documentName should be created with :property set to :value
     */
    public function documentShouldBeCreatedWithPropertySetToValue($documentName, $property, $value)
    {
        $dm = $this->getContainer()->get('doctrine_mongodb')->getManager();

        if(ucfirst($documentName) == 'User') {
            $document = $dm->getRepository('SyslaWeeNeedToTalkWnttUserBundle:'.ucfirst($documentName))
                ->findOneBy(array($property => $value));
        } else {
            $document = $dm->getRepository('SyslaWeeNeedToTalkWnttApiBundle:'.ucfirst($documentName))
                ->findOneBy(array($property => $value));
        }

        if (empty($document)) {
            throw new \Exception(sprintf('Document %s hasn\'t been created', $documentName));
        }

        $this->getParameterBag()->set(ucfirst($documentName).'_last_created', $document->getId());
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

    protected function extractFromParameterBag($uri)
    {
        $uri = $this->getParameterBag()->replace($uri);
        $uri = str_replace(['{', '}'], '', $uri);
        return $uri;
    }

    protected function assertDocumentHasProperty($document, $property)
    {
        if(!isset($document->$property)) {
            throw new Exception\NotFoundPropertyException($property);
        }
    }

    protected function assertDocumentHasPropertyWithValue($document, $property, $expectedValue)
    {
        $this->assertDocumentHasProperty($document, $property);
        if($document->$property != $expectedValue) {
            throw new Exception\IncorrectPropertyValueException($property, $expectedValue, $document->$property);
        }
    }

    protected function assertDocumentHasPropertyWithBooleanValue($document, $property, $expectedValue)
    {
        $expectedBoolean = ($expectedValue == 'true' ? true : false);
        $this->assertDocumentHasProperty($document, $property);
        if($document->$property !== $expectedBoolean) {
            throw new Exception\IncorrectPropertyValueException($property, $expectedValue, $document->$property === true ? 'true' : 'false');
        }
    }

    protected function ensureCategoryExists($categoryData)
    {
        $dm = $this->getContainer()->get('doctrine_mongodb')->getManager();

        $category = $dm->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Category')
            ->findOneByName($categoryData['name']);

        if(empty($category)) {
            $category = new Document\Category();
            $category->setName($categoryData['name']);

            $dm->persist($category);
            $dm->flush();
        }

        $this->getParameterBag()->set('Category_'.$categoryData['identifiedBy'], $category->getId());
    }

    protected function ensureCompanyExists($companyData)
    {
        $dm = $this->getContainer()->get('doctrine_mongodb')->getManager();

        $company = $dm->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Company')
            ->findOneByName($companyData['name']);

        if(empty($company)) {
            $company = new Document\Company();
            $company->setName($companyData['name']);
            $company->setLogoUrl(@$companyData['logoUrl']);
            $company->setWebsiteUrl(@$companyData['websiteUrl']);

            $dm->persist($company);
            $dm->flush();
        }

        $this->getParameterBag()->set('Company_'.$companyData['identifiedBy'], $company->getId());
    }

    protected function ensureEventExists($eventData)
    {
        $dm = $this->getContainer()->get('doctrine_mongodb')->getManager();

        $event = $dm->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Event')
            ->findOneByName($eventData['name']);

        if(empty($event)) {
            $event = new Document\Event();
            $event->setName($eventData['name']);
            $event->setLocation(@$eventData['location']);
            $event->setDateStart(new \DateTime($eventData['dateStart']));
            $event->setDateEnd(new \DateTime($eventData['dateEnd']));

            $dm->persist($event);
            $dm->flush();
        }

        $this->getParameterBag()->set('Event_'.$eventData['identifiedBy'], $event->getId());
    }

    protected function ensureStandExists($standData)
    {
        $dm = $this->getContainer()->get('doctrine_mongodb')->getManager();

        $stand = $dm->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Stand')
            ->findOneByNumber($standData['number']);

        if(empty($stand)) {
            $stand = new Document\Stand();
            $stand->setNumber($standData['number']);
            $stand->setHall(@$standData['hall']);

            $eventId = $this->getParameterBag()->get('Event_'.$standData['event']);
            $event = $dm->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Event')
                ->findOneById($eventId);
            $stand->setEvent($event);

            if(isset($standData['company'])) {
                $companyId = $this->getParameterBag()->get('Company_'.$standData['company']);
                $company = $dm->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Company')
                    ->findOneById($companyId);
                $stand->setCompany($company);
            }

            $dm->persist($stand);
            $dm->flush();
        }

        $this->getParameterBag()->set('Stand_'.$standData['identifiedBy'], $stand->getId());
    }

    protected function ensurePresentationExists($presentationData)
    {
        $dm = $this->getContainer()->get('doctrine_mongodb')->getManager();

        $presentation = $dm->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Presentation')
            ->findOneByVideoUrl($presentationData['videoUrl']);

        if(empty($presentation)) {
            $presentation = new Document\Presentation();
            $presentation->setVideoUrl($presentationData['videoUrl']);
            $presentation->setDescription(@$presentationData['description']);
            $presentation->setIsPremium(@$presentationData['isPremium'] == 'true' ? true : false);

            $standId = $this->getParameterBag()->get('Stand_'.$presentationData['stand']);
            $stand = $dm->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Stand')
                ->findOneById($standId);
            $presentation->setStand($stand);

            if(isset($presentationData['company'])) {
                $companyId = $this->getParameterBag()->get('Company_'.$presentationData['company']);
                $company = $dm->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Company')
                    ->findOneById($companyId);
                $presentation->setCompany($company);
            }

            if(isset($presentationData['categories'])) {
                $categories = [];
                foreach(explode(';', $presentationData['categories']) as $categoryName) {
                    $categoryId = $this->getParameterBag()->get('Category_'.$categoryName);
                    $category = $dm->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Category')
                        ->findOneById($categoryId);
                    if(!empty($category)) {
                        $categories[] = $category;
                    }
                }
                $presentation->setCategories($categories);
            }

            $dm->persist($presentation);
            $dm->flush();
        }

        $this->getParameterBag()->set('Presentation_'.$presentationData['identifiedBy'], $presentation->getId());
    }

    protected function ensureUserExists($userData)
    {
        $dm = $this->getContainer()->get('doctrine_mongodb')->getManager();

        $user = $dm->getRepository('SyslaWeeNeedToTalkWnttUserBundle:User')
            ->findOneByUsername($userData['username']);

        if(empty($user)) {
            $user = new User();
            $user->setUsername($userData['username']);
            $user->setEmail($userData['email']);
            if(isset($userData['roles'])) {
                $user->setRoles(explode(';', $userData['roles']));
            }

            if(isset($userData['company'])) {
                $companyId = $this->getParameterBag()->get('Company_'.$userData['company']);
                $company = $dm->getRepository('SyslaWeeNeedToTalkWnttApiBundle:Company')
                    ->findOneById($companyId);
                $user->setCompany($company);
            }

            $dm->persist($user);
            $dm->flush();
        }

        $this->getParameterBag()->set('User_'.$userData['identifiedBy'], $user->getId());
    }

}
