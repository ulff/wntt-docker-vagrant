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
     * @Given I am on main page
     * @Given I go to main page
     */
    public function iAmOnMainPage()
    {
        $this->visit('/app_dev.php');
    }

    /**
     * @Given I am on :formLabel form
     */
    public function iAmOnForm($formLabel)
    {
        list($action, $document) = explode(' ', $formLabel);
        $this->visit("/app_dev.php/admin/weneedtotalk/wnttapi/$document/$action");
    }

    /**
     * @Then I should see form notification :message
     */
    public function iShouldSeeFormNotification($message)
    {
        $this->assertPageContainsText($message);
    }


}
