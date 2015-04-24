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
