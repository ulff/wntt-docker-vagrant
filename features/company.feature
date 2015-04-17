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

  Scenario: browse companies
    Given "Company" exists with data
      | Name        | Company 2nd              |
      | Website URL | http://c2.com            |
      | Logo URL    | http://c2.com/!Pomorskie |
    When I go to "company" list
    Then I should see "Company 2nd"
    And I should see "http://c2.com"
    And I should see "http://c2.com/!Pomorskie"