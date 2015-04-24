Feature: adding events
  In order to organize events
  As an admin
  I want to be able to manage event list

  Background:
    Given I am logged in as super-admin

  Scenario: create event
    Given I am on "create event" form
    When I fill in the following:
      | Name        | E1          |
      | Location    | Atlantis    |
    And I press "Create"
    Then I should see form notification "successfully created"

  Scenario: browse events
    Given "Event" exists with data
      | Name      | Olive Oil               |
      | Location  | Olivia Buisness Centre  |
    When I go to "event" list
    Then I should see "Olive Oil"
    And I should see "Olivia Buisness Centre"

  Scenario: removing all events
    Given I am on "event" list
    When I check "all_elements"
    And I press "OK"
    And I press "Yes, execute"
    Then I should see form notification "successfully deleted"
