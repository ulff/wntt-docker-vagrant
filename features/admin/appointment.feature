Feature: adding user appointments
  In order to organize users schedule
  As an admin
  I want to be able to manage appointments

  Background:
    Given I am logged in as super-admin
    And "Event" exists with data
      | Name        | Event App 97  |
      | Location    | Mo i Rana     |
    And "Stand" exists with data
      | Hall        | S         |
      | Number      | 32        |
    And "Company" exists with data
      | Name        | Company App          |
      | Website URL | http://capp.com      |
      | Logo URL    | http://capp.com/logo |
    And "Presentation" exists with data
      | Video URL   | http://video/97   |
      | Description | Some description  |

  Scenario: removing all events
    Given I am on "event" list
    When I check "all_elements"
    And I press "OK"
    And I press "Yes, execute"
    Then I should see form notification "successfully deleted"

  Scenario: create appointment
    When I am on "create appointment" form
    And I select "Event App 97" from "Event"
    And I select "http://video/97" from "Presentation"
    And I press "Create"
    Then I should see form notification "successfully created"

  Scenario: browse appointments
    When I go to "appointment" list
    Then I should see "http://video/97"
    And I should see "Event App 97"

  Scenario: removing all appointments
    Given I am on "appointment" list
    When I check "all_elements"
    And I press "OK"
    And I press "Yes, execute"
    Then I should see form notification "successfully deleted"
