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
    When I make request "GET" "/api/v1/users/{User_username_api}"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "id" field
    And the repsonse JSON should have "username" field with value "username_api"
    And the repsonse JSON should have "email" field with value "user1@email.api"

  Scenario: create user
    Given I am authorized client with username "admin" and password "admin"
    When I make request "POST" "/api/v1/users" with parameter-bag params:
      | username        | user_api_created      |
      | password        | password              |
      | email           | user@api              |
      | company         | Company_Company Api   |
      | isAdmin         | true                  |
      | phoneNumber     | 668 678               |
    Then "User" should be created with "username" set to "user_api_created"
    And the response status code should be 201
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "id" field
    And the repsonse JSON should have "username" field with value "user_api_created"
    And the repsonse JSON should have "email" field with value "user@api"
    And the repsonse JSON should have "phone_number" field with value "668 678"

  Scenario: update user
    Given I am authorized client with username "admin" and password "admin"
    When I make request "PUT" "/api/v1/users/{User_last_created}" with parameter-bag params:
      | username        | user_api_updated      |
      | password        | password              |
      | email           | user2@api             |
      | company         | Company_Company Api   |
      | isAdmin         | true                  |
      | phoneNumber     | 668 678 2             |
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "id" field
    And the repsonse JSON should have "username" field with value "user_api_updated"
    And the repsonse JSON should have "email" field with value "user2@api"
    And the repsonse JSON should have "phone_number" field with value "668 678 2"

  Scenario: delete user
    Given I am authorized client with username "admin" and password "admin"
    When I make request "DELETE" "/api/v1/users/{User_last_created}"
    Then the response status code should be 204
    And I make request "HEAD" "/api/v1/users/{User_last_created}"
    And the response status code should be 404

  Scenario Outline: do not create user when empty or invalid param
    Given I am authorized client with username "admin" and password "admin"
    When I make request "POST" "/api/v1/users" with parameter-bag params:
      | username        | <username>      |
      | password        | <password>      |
      | email           | <email>         |
      | company         | <company>       |
      | isAdmin         | <isAdmin>       |
      | phoneNumber     | <phoneNumber>   |
    Then the response status code should be 400
    And the response should be JSON
    And the repsonse JSON should have "error" field

  Examples:
    | username          | password            | email             | company             | isAdmin       | phoneNumber |
    |                   | password            | user@api          | Company_Company Api | true          | 668 678     |
    | user_api_created  |                     | user@api          | Company_Company Api | true          | 668 678     |
    | user_api_created  | password            |                   | Company_Company Api | true          | 668 678     |
    | user_api_created  | password            | user@api          | not-existing        | true          | 668 678     |

  Scenario: cannot update user without user context
    When I make request "PUT" "/api/v1/users/{User_username_api}"
    Then the response status code should be 403

  Scenario: cannot delete user without user context
    When I make request "DELETE" "/api/v1/users/{User_username_api}"
    Then the response status code should be 403

  Scenario: user without admin proviledges cannot update not himself
    Given I am authorized client with username "user" and password "user"
    When I make request "PUT" "/api/v1/users/{User_username_api}"
    Then the response status code should be 403
    And the response should contain "Cannot affect not your account"

  Scenario: user without admin proviledges cannot delete not himself
    Given I am authorized client with username "user" and password "user"
    When I make request "DELETE" "/api/v1/users/{User_username_api}"
    Then the response status code should be 403
    And the response should contain "Cannot affect not your account"