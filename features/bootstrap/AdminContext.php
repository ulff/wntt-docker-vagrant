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
     * @Given I go to :formLabel form
     */
    public function iAmOnForm($formLabel)
    {
        list($action, $document) = explode(' ', $formLabel);
        $this->visit("/app_dev.php/admin/weneedtotalk/wnttapi/$document/$action");
    }

    /**
     * @Given Event exists with data
     */
    public function eventExistsWithData(TableNode $table)
    {
        $this->iAmOnForm('create event');
        foreach($table->getTable() as $row) {
            $this->fillField($row[0], $row[1]);
        }
        $this->pressButton('Create');
    }

    /**
     * @When I am on :document list
     * @When I go to :document list
     */
    public function iGoToList($document)
    {
        $this->visit("/app_dev.php/admin/weneedtotalk/wnttapi/$document/list");
    }


    /**
     * @Then I should see form notification :message
     */
    public function iShouldSeeFormNotification($message)
    {
        $this->assertPageContainsText($message);
    }

}
