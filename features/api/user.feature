Feature: getting users through API
  In order to get information about users
  As an authorized client application
  I want to be able to retrieve user list

  Background:
    Given I am authorized client
    And following "Company" exists:
      | identifiedBy  | Company Api                 |
      | name          | Company Api                 |
      | websiteUrl    | http://company.api          |
      | logoUrl       | http://company.api/logo.png |

    And following "User" exists:
      | identifiedBy  | username_api    |
      | username      | username_api    |
      | email         | user1@email.api |
      | roles         | ROLE_USER       |
      | company       | Company Api     |

    And following "User" exists:
      | identifiedBy  | admin_api             |
      | username      | admin_api             |
      | email         | admin1@email.api      |
      | roles         | ROLE_USER;ROLE_ADMIN  |

  Scenario: get list of all users
    When I make request "GET" "/api/v1/users"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a collection

  Scenario: get one user
    When I make request "GET" "/api/v1/users/{User:username_api}"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "id" field
    And the repsonse JSON should have "username" field with value "username_api"
    And the repsonse JSON should have "email" field with value "user1@email.api"

