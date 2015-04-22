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

  Scenario: browse users
    Given "User" exists with data
      | Username      | test-scenario3  |
      | Email         | scenario3@wntt  |
      | Password      | password3       |
      | Phone number  | 001 002 003     |
    When I go to "user" list
    Then I should see "test-scenario3"
    And I should see "scenario3@wntt"

  Scenario: create user with admin privileges and log him in
    Given "user" exists with data
      | Username      | test-scenario4  |
      | Email         | scenario4@wntt  |
      | Password      | password4       |
      | Phone number  | 001 002 004     |
      | Roles         | ROLE_ADMIN      |
    And I am logged out
    When I go to login page
    And I fill in "Username" with "test-scenario4"
    And I fill in "Password" with "password4"
    And I press "Login"
    Then I should be on "app_dev.php/admin/dashboard"
    And I should not see "Access Denied"

  Scenario: create user without admin privileges and log him in
    Given "user" exists with data
      | Username      | test-scenario5  |
      | Email         | scenario5@wntt  |
      | Password      | password5       |
      | Phone number  | 001 002 005     |
      | Roles         | ROLE_USER       |
    And I am logged out
    When I go to login page
    And I fill in "Username" with "test-scenario5"
    And I fill in "Password" with "password5"
    And I press "Login"
    Then I should be on "app_dev.php/admin/dashboard"
    And I should see "Access Denied"
