Feature: adding companies
  In order to have event participants
  As an admin
  I want to be able to manage companies list

  Scenario: create company
    Given I am on "create company" form
    When I fill in the following:
      | Name        | C1                        |
      | Website URL | http://c1.com             |
      | Logo URL    | http://c1/static/logo.png |
    And I press "Create"
    Then I should see form notification "successfully created"