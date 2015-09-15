Feature: non-RESTful endpoint for creating 3 documents in one request
  In order to simply register user, company and presentation
  As an authorized client application
  I want to be able to register them in one POST request

  Background:
    Given I am authorized client
    And following "Event" exists:
      | identifiedBy  | Event_Api_1 |
      | name          | Event Api 1 |
      | location      | Honningsvag |
      | dateStart     | 2014-02-02  |
      | dateEnd       | 2014-02-04  |
    And following "Category" exists:
      | identifiedBy | Gas |
      | name         | Gas |
    And following "Category" exists:
      | identifiedBy | Oil |
      | name         | Oil |

  Scenario: successful registration
    When I make request "POST" "/api/v1/quick_registration/" with parameter-bag params:
      | user_email              | olaf.galazka@schibsted.pl |
      | user_fullname           | Olaf Galazka              |
      | user_phone              | 609 xxx yyy               |
      | company_name            | STP                       |
      | presentation_name       | WNTT_testcase             |
      | presentation_hall       | Olivia Four               |
      | presentation_number     | 4                         |
      | presentation_event      | Event_Event_Api_1         |
      | presentation_categories | Category_Gas              |
    Then "User" should be created with "username" set to "olaf.galazka@schibsted.pl"
    And "Company" should be created with "name" set to "STP"
    And "Presentation" should be created with "name" set to "WNTT_testcase"
    And the response status code should be 201
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "user" field
    And the repsonse JSON should have "company" field
    And the repsonse JSON should have "presentation" field
    And I make request "HEAD" "/api/v1/presentations/{Presentation_last_created}"
    And the response status code should be 200
    And I make request "HEAD" "/api/v1/users/{User_last_created}"
    And the response status code should be 200
    And I make request "HEAD" "/api/v1/companies/{Company_last_created}?inclDisabled=true"
    And the response status code should be 200

  Scenario: delete user
    Given I am authorized client with username "admin" and password "admin"
    When I make request "DELETE" "/api/v1/users/{User_last_created}"
    And I make request "HEAD" "/api/v1/users/{User_last_created}"
    Then the response status code should be 404

  Scenario: delete presentation
    Given I am authorized client with username "admin" and password "admin"
    When I make request "DELETE" "/api/v1/presentations/{Presentation_last_created}"
    And I make request "HEAD" "/api/v1/presentations/{Presentation_last_created}"
    Then the response status code should be 404

  Scenario: delete company
    Given I am authorized client with username "admin" and password "admin"
    When I make request "DELETE" "/api/v1/companies/{Company_last_created}"
    And I make request "HEAD" "/api/v1/companies/{Company_last_created}?inclDisabled=true"
    Then the response status code should be 404

  Scenario Outline: should return http 400 on empty or invalid params
    When I make request "POST" "/api/v1/quick_registration/" with parameter-bag params:
      | user_email              | <user_email> |
      | user_fullname           | <user_fullname> |
      | user_phone              | <user_phone> |
      | company_name            | <company_name> |
      | presentation_name       | <presentation_name> |
      | presentation_hall       | <presentation_hall> |
      | presentation_number     | <presentation_number> |
      | presentation_event      | <presentation_event> |
      | presentation_categories | <presentation_categories> |
    Then the response status code should be 400
    And the response should be JSON
    And the repsonse JSON should have "error" field

  Examples:
      | user_email        | user_fullname | user_phone  | company_name  | presentation_name  | presentation_hall | presentation_number | presentation_event | presentation_categories |
      |                   | Olaf Galazka  | 609 xxx yyy | STP1          | WNTT_testcase1     | Olivia Four       | 4                   | Event_Event_Api_1  | Category_Gas            |
      | wntt1@gmail.com   |               | 609 xxx yyy | STP2          | WNTT_testcase2     | Olivia Four       | 4                   | Event_Event_Api_1  | Category_Gas            |
      | wntt2@gmail.com   | Olaf Galazka  | 609 xxx yyy |               | WNTT_testcase3     | Olivia Four       | 4                   | Event_Event_Api_1  | Category_Gas            |
      | wntt3@gmail.com   | Olaf Galazka  | 609 xxx yyy | STP3          |                    | Olivia Four       | 4                   | Event_Event_Api_1  | Category_Gas            |
      | wntt4@gmail.com   | Olaf Galazka  | 609 xxx yyy | STP4          | WNTT_testcase4     | Olivia Four       | 4                   |                    | Category_Gas            |
      | wntt5@gmail.com   | Olaf Galazka  | 609 xxx yyy | STP5          | WNTT_testcase5     | Olivia Four       | 4                   | not-existing       | Category_Gas            |

  Scenario Outline: should return http 409 on duplicated documents
    Given following "User" exists:
      | identifiedBy  | wntt_test@gmail.com |
      | username      | wntt_test@gmail.com |
      | email         | wntt_test@gmail.com |
    And following "Company" exists:
      | identifiedBy  | STP7  |
      | name          | STP7  |
    When I make request "POST" "/api/v1/quick_registration/" with parameter-bag params:
      | user_email              | <user_email> |
      | user_fullname           | <user_fullname> |
      | user_phone              | <user_phone> |
      | company_name            | <company_name> |
      | presentation_name       | <presentation_name> |
      | presentation_hall       | <presentation_hall> |
      | presentation_number     | <presentation_number> |
      | presentation_event      | <presentation_event> |
      | presentation_categories | <presentation_categories> |
    Then the response status code should be 409
    And the response should be JSON
    And the repsonse JSON should have "error" field

  Examples:
    | user_email            | user_fullname | user_phone  | company_name  | presentation_name  | presentation_hall | presentation_number | presentation_event | presentation_categories |
    | wntt_test@gmail.com   | Olaf Galazka  | 609 xxx yyy | STP9          | WNTT_testcase1     | Olivia Four       | 4                   | Event_Event_Api_1  | Category_Gas            |
    | wntt8@gmail.com       | Olaf Galazka  | 609 xxx yyy | STP7          | WNTT_testcase1     | Olivia Four       | 4                   | Event_Event_Api_1  | Category_Gas            |
