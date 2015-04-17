Feature: adding users
  In order to give others access to administration
  As an admin
  I want to be able to manage users list

  Background:
    Given I am logged in as super-admin

  Scenario: create user
    Given I am on "create user" form
    When I fill in the following:
      | Username      | test-scenario1  |
      | Email         | scenario1@wntt  |
      | Password      | password1       |
      | Phone number  | 001 002 001     |
    And I press "Create"
    Then I should see form notification "successfully created"

  Scenario: create user and log him in
    Given "user" exists with data
      | Username      | test-scenario2  |
      | Email         | scenario2@wntt  |
      | Password      | password2       |
      | Phone number  | 001 002 002     |
    And I am logged out
    When I go to login page
    And I fill in "Username" with "test-scenario2"
    And I fill in "Password" with "password2"
    And I press "Login"
    Then I should not see "Invalid credentials"
