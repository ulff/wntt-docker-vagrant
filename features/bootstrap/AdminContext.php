<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;

/**
 * Defines application features from the specific context.
 */
class AdminContext extends MinkContext implements Context, SnippetAcceptingContext
{

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
        $this->visit('/app_dev.php/admin/dashboard');
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

        $this->visit("/app_dev.php/admin/weneedtotalk/$bundle/$document/$action");
    }

    /**
     * @Given :document exists with data
     */
    public function documentExistsWithData($document, TableNode $table)
    {
        $this->iAmOnForm('create '.strtolower($document));
        foreach($table->getTable() as $row) {
            $this->fillField($row[0], $row[1]);
        }
        $this->pressButton('Create');
    }

    /**
     * @Given /^I (?:have|am) logged out$/
     */
    public function iHaveLoggedOut()
    {
        $this->visit('/app_dev.php/logout');
    }

    /**
     * @When I go to login page
     */
    public function iGoToLoginPage()
    {
        $this->visit('/app_dev.php/login');
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

        $this->visit("/app_dev.php/admin/weneedtotalk/$bundle/$document/list");
    }


    /**
     * @Then I should see form notification :message
     */
    public function iShouldSeeFormNotification($message)
    {
        $this->assertPageContainsText($message);
    }

}
