Feature: adding presentations
  In order to have event contents
  As an admin
  I want to be able to manage presentations list

  Background:
    Given I am logged in as super-admin

  Scenario: create presentation
    Given "Event" exists with data
      | Name        | Event Two |
      | Location    | Bergen    |
    And "Company" exists with data
      | Name        | Company 4th        |
      | Website URL | http://c4.com      |
      | Logo URL    | http://c4.com/logo |
    And I am on "create presentation" form
    When I fill in the following:
      | Video URL   | http://video/1    |
      | Name        | Pres name         |
      | Hall        | F                 |
      | Number      | 19                |
      | Description | Some description  |
    And I select "Company 4th" from "Company"
    And I check "Is premium"
    And I press "Create"
    Then I should see form notification "successfully created"

  Scenario: do not create presentation with empty video url
    Given I am on "create presentation" form
    When I fill in the following:
      | Name        | Pres name |
      | Description | Some description  |
    And I press "Create"
    Then I should be on "create presentation" form
    And I should not see "successfully created"

  Scenario: do not create presentation with empty name
    Given I am on "create presentation" form
    When I fill in the following:
      | Video URL   | http://video/x    |
      | Description | Some description  |
    And I press "Create"
    Then I should be on "create presentation" form
    And I should not see "successfully created"

  Scenario: browse presentations
    Given "presentation" exists with data
      | Video URL   | http://video/2    |
      | Name        | Name of video 2   |
      | Hall        | X                 |
      | Number      | 12                |
    When I go to "presentation" list
    Then I should see "Name of video 2"

  Scenario: removing all presentations
    Given I am on "presentation" list
    When I check "all_elements"
    And I press "OK"
    And I press "Yes, execute"
    Then I should see form notification "successfully deleted"