Feature: adding categories
  In order to have events scopes organized
  As an admin
  I want to be able to manage categories list

  Background:
    Given I am logged in as super-admin

  Scenario: create category
    Given I am on "create category" form
    When I fill in the following:
      | Name        | Oil      |
    And I press "Create"
    Then I should see form notification "successfully created"

  Scenario: do not create category with empty name
    Given I am on "create category" form
    And I press "Create"
    Then I should be on "create category" form
    And I should not see "successfully created"

  Scenario: browse categories
    Given "category" exists with data
      | Name        | Petrol |
    When I go to "category" list
    Then I should see "Petrol"

  Scenario: removing all categories
    Given I am on "category" list
    When I check "all_elements"
    And I press "OK"
    And I press "Yes, execute"
    Then I should see form notification "successfully deleted"
