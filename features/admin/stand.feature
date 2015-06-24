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

  Scenario: allow creating stand with empty hall
    Given I am on "create stand" form
    When I fill in the following:
      | Number      | S3        |
    And I press "Create"
    Then I should see form notification "successfully created"

  Scenario: do not create stand with empty number
    Given I am on "create stand" form
    When I fill in the following:
      | Hall        | Feature B |
    And I press "Create"
    Then I should be on "create stand" form
    And I should not see "successfully created"

  Scenario: browse stands
    Given "Stand" exists with data
      | Hall        | Feature A |
      | Number      | FA-S2     |
    When I go to "stand" list
    Then I should see "Feature A"
    And I should see "FA-S2"

  Scenario: assign company to stand
    Given "Company" exists with data
      | identifiedBy  | Company_with_stand        |
      | Name          | Company with stand        |
      | Website URL   | http://com.pany           |
      | Logo URL      | http://com.pany/logo      |
    And "Stand" exists with data
      | identifiedBy  | FAS1              |
      | Hall          | Feature A         |
      | Number        | FA-S1             |
    When I am on edit "stand" "FAS1" form
    And I select "Company with stand" from "Company"
    And I press "Update"
    Then I should see form notification "successfully updated"

  Scenario: removing all stands
    Given I am on "stand" list
    When I check "all_elements"
    And I press "OK"
    And I press "Yes, execute"
    Then I should see form notification "successfully deleted"
