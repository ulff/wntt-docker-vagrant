<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;
use Codifico\ParameterBagExtension\Context\ParameterBagDictionary;

/**
 * Defines application features from the specific context.
 */
class AdminContext extends MinkContext implements Context, SnippetAcceptingContext
{

    use ParameterBagDictionary;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
    }

    /**
     * @Given /^I (?:am on|go to) dashboard$/
     */
    public function iAmOnDashboard()
    {
        $this->visit('/app_behat.php/admin/dashboard');
    }

    /**
     * @Given /^I am logged in as super[- ]?admin$/
     */
    public function iAmLoggedInAsSuperAdmin()
    {
        $this->iGoToLoginPage();
        $this->fillField('Username', 'admin');
        $this->fillField('Password', 'admin');
        $this->pressButton('Login');
    }

    /**
     * @Given /^I (?:am on|go to) "(.+)" form$/
     */
    public function iAmOnForm($formLabel)
    {
        list($action, $document) = explode(' ', $formLabel);

        switch($document) {
            case 'user':
                $bundle = 'wnttuser';
                break;
            default:
                $bundle = 'wnttapi';
        }

        $this->visit("/app_behat.php/admin/weneedtotalk/$bundle/$document/$action");
    }

    /**
     * @Given :document exists with data
     */
    public function documentExistsWithData($document, TableNode $table)
    {
        $identifiedBy = uniqid();
        $this->iAmOnForm('create '.strtolower($document));
        foreach($table->getRowsHash() as $field => $value) {
            if($field == 'identifiedBy') {
                $identifiedBy = $value;
            } else {
                $this->fillField($field, $value);
            }
        }
        $this->pressButton('Create');

        $editPageUrl = explode('/', $this->getSession()->getCurrentUrl());
        if(array_pop($editPageUrl) == 'edit') {
            $this->getParameterBag()->set(ucfirst($document).'_'.$identifiedBy, array_pop($editPageUrl));
        }

    }

    /**
     * @Given I am on edit :document :identifiedBy form
     * @Given I go to edit :document :identifiedBy form
     */
    public function iAmOnEditForm($document, $identifiedBy)
    {
        $mongoId = $this->getParameterBag()->get(ucfirst($document).'_'.$identifiedBy);

        switch($document) {
            case 'user':
                $bundle = 'wnttuser';
                break;
            default:
                $bundle = 'wnttapi';
        }

        $this->visit("/app_behat.php/admin/weneedtotalk/$bundle/$document/$mongoId/edit");
    }

    /**
     * @Given /^I (?:have|am) logged out$/
     */
    public function iHaveLoggedOut()
    {
        $this->visit('/app_behat.php/logout');
    }

    /**
     * @When I go to login page
     */
    public function iGoToLoginPage()
    {
        $this->visit('/app_behat.php/login');
    }

    /**
     * @When /^I (?:am on|go to) "(.+)" list$/
     */
    public function iGoToList($document)
    {
        switch($document) {
            case 'user':
                $bundle = 'wnttuser';
                break;
            default:
                $bundle = 'wnttapi';
        }

        $this->visit("/app_behat.php/admin/weneedtotalk/$bundle/$document/list");
    }

    /**
     * @When I check following items from grid:
     */
    public function iCheckFollowingItemsFromGrid(TableNode $table)
    {
        $editPageUrl = explode('/', $this->getSession()->getCurrentUrl());
        $document = $editPageUrl[count($editPageUrl)-2];

        foreach($table->getRows() as $row) {
            $mongoId = $this->getParameterBag()->get(ucfirst($document).'_'.reset($row));
            $checkboxList = $this->getSession()->getPage()->findAll('xpath', "//input[@value='$mongoId']");
            $checkbox = reset($checkboxList);
            $checkbox->check();
        }
    }

    /**
     * @Then I should see form notification :message
     */
    public function iShouldSeeFormNotification($message)
    {
        $this->assertPageContainsText($message);
    }

    /**
     * @Then I should be on :formLabel form
     */
    public function iShouldBeOnForm($formLabel)
    {
        list($action, $document) = explode(' ', $formLabel);

        switch($document) {
            case 'user':
                $bundle = 'wnttuser';
                break;
            default:
                $bundle = 'wnttapi';
        }

        $this->assertPageAddress("/app_behat.php/admin/weneedtotalk/$bundle/$document/$action");
    }

}