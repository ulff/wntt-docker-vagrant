Feature: adding events
  In order to organize events
  As an admin
  I want to be able to manage event list

  Scenario: create event
    Given I am on "create event" form
    And print last response
    When I fill in the following:
      | Name        | E1          |
      | Location    | Atlantis    |
    And I press "Create"
    Then I should see form notification "successfully created"
