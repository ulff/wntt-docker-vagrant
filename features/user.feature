Feature: adding users
  In order to give others access to administration
  As an admin
  I want to be able to manage users list

  Background:
    Given I am logged in as super-admin

  Scenario: create user and log him in
    Given "user" exists with data
      | identifiedBy  | test-scenario2  |
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
      | identifiedBy  | test-scenario3  |
      | Username      | test-scenario3  |
      | Email         | scenario3@wntt  |
      | Password      | password3       |
      | Phone number  | 001 002 003     |
    When I go to "user" list
    Then I should see "test-scenario3"
    And I should see "scenario3@wntt"

  Scenario: create user with admin privileges and log him in
    Given "user" exists with data
      | identifiedBy  | test-scenario4  |
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
      | identifiedBy  | test-scenario5  |
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

  Scenario: delete user from edit form
    Given I am on edit "user" "test-scenario2" form
    When I follow "Delete"
    And I press "Yes, delete"
    Then I should see form notification "deleted successfully"

  Scenario: assigning company to user
    Given "Company" exists with data
      | Name          | Company with user   |
      | Website URL   | http://cu.com       |
      | Logo URL      | http://cu.com/logo  |
    And "User" exists with data
      | identifiedBy  | test-scenario6  |
      | Username      | test-scenario6  |
      | Email         | scenario6@wntt  |
      | Password      | password6       |
      | Phone number  | 001 002 006     |
    And I am on edit "user" "test-scenario6" form
    When I select "Company with user" from "Company"
    And I press "Update"
    Then I should see form notification "successfully updated"

  Scenario: set user as contact person
    Given I am on edit "user" "test-scenario6" form
    And I check "Is contact person"
    And I press "Update"
    Then I should see form notification "successfully updated"
    And the "Is contact person" checkbox should be checked

  Scenario: set user is not a contact person
    Given I am on edit "user" "test-scenario6" form
    And I uncheck "Is contact person"
    And I press "Update"
    Then I should see form notification "successfully updated"
    And the "Is contact person" checkbox should not be checked

  Scenario: cannot set user without company as a contact person
    Given "User" exists with data
      | identifiedBy  | test-scenario7  |
      | Username      | test-scenario7  |
      | Email         | scenario7@wntt  |
      | Password      | password7       |
      | Phone number  | 001 002 007     |
    And I am on edit "user" "test-scenario7" form
    And I check "Is contact person"
    And I press "Update"
    Then I should see "Invalid User state"

  Scenario: user can be assigned to company and set as contact person
    Given "User" exists with data
      | identifiedBy  | test-scenario8  |
      | Username      | test-scenario8  |
      | Email         | scenario8@wntt  |
      | Password      | password8       |
      | Phone number  | 001 002 008     |
    And I am on edit "user" "test-scenario8" form
    And I select "Company with user" from "Company"
    And I check "Is contact person"
    And I press "Update"
    Then I should see form notification "successfully updated"
    And the "Is contact person" checkbox should be checked

  Scenario: when user is being unassigned from company he cannot be a contact person
    When I go to edit "user" "test-scenario8" form
    And I select "" from "Company"
    And I press "Update"
    Then I should see "Invalid User state"

  Scenario: batch delete selected users
    Given I am on "user" list
    When I check following items from grid:
      | test-scenario3 |
      | test-scenario4 |
      | test-scenario5 |
      | test-scenario6 |
      | test-scenario7 |
      | test-scenario8 |
    And I press "OK"
    And I press "Yes, execute"
    Then I should see form notification "successfully deleted"
