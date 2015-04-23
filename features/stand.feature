Feature: adding stands to events
  In order to organize event space
  As an admin
  I want to be able to manage each event stands list

  Background:
    Given I am logged in as super-admin
    And "Event" exists with data
      | Name        | Event One |
      | Location    | Oslo      |

  Scenario: create stand
    Given I am on "create stand" form
    When I fill in the following:
      | Hall        | Feature A |
      | Number      | FA-S1     |
    And I press "Create"
    Then I should see form notification "successfully created"

  Scenario: browse companies
    Given "Stand" exists with data
      | Hall        | Feature A |
      | Number      | FA-S2     |
    When I go to "stand" list
    Then I should see "Feature A"
    And I should see "FA-S2"