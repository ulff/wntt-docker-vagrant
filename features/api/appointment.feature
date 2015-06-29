Feature: managing appointments using API
  In order to manage user schedules for events
  As an authorized client application
  I want to be able to make all operations on appointments

  Background:
    Given I am authorized client
    And following "Event" exists:
      | identifiedBy  | evt1        |
      | name          | Event 1     |
      | location      | Honningsvag |
      | dateStart     | 2014-02-02  |
      | dateEnd       | 2014-02-04  |
    And following "Event" exists:
      | identifiedBy  | evt2        |
      | name          | Event Api 2 |
      | location      | Tromso      |
      | dateStart     | 2015-07-31  |
      | dateEnd       | 2015-08-23  |
    And following "Company" exists:
      | identifiedBy  | com1        |
      | name          | Company 1   |
    And following "Company" exists:
      | identifiedBy  | com2        |
      | name          | Company 2   |
    And following "Stand" exists:
      | identifiedBy  | evt1_s1       |
      | number        | 133           |
      | hall          | A             |
      | event         | evt1          |
      | company       | com1          |
    And following "Stand" exists:
      | identifiedBy  | evt1_s2       |
      | number        | 134           |
      | hall          | A             |
      | event         | evt1          |
      | company       | com1          |
    And following "Stand" exists:
      | identifiedBy  | evt1_s3       |
      | number        | 135           |
      | hall          | A             |
      | event         | evt1          |
      | company       | com2          |
    And following "Stand" exists:
      | identifiedBy  | evt2_s1       |
      | number        | 1             |
      | hall          | D             |
      | event         | evt2          |
      | company       | com1          |
    And following "Stand" exists:
      | identifiedBy  | evt2_s2       |
      | number        | 34            |
      | hall          | A             |
      | event         | evt2          |
      | company       | com1          |
    And following "Stand" exists:
      | identifiedBy  | evt2_s3       |
      | number        | 5             |
      | hall          | B             |
      | event         | evt2          |
      | company       | com2          |
    And following "User" exists:
      | identifiedBy  | user9_api       |
      | username      | user9_api       |
      | email         | user9@email.api |
      | roles         | ROLE_USER       |
      | company       | com1            |
    And following "User" exists:
      | identifiedBy  | admin9_api            |
      | username      | admin9_api            |
      | email         | admin9@email.api      |
      | roles         | ROLE_USER;ROLE_ADMIN  |
    And following "Category" exists:
      | identifiedBy | Gas |
      | name         | Gas |
    And following "Category" exists:
      | identifiedBy | Oil |
      | name         | Oil |
    And following "Presentation" exists:
      | identifiedBy | pres1                    |
      | videoUrl     | http://company.api/12345 |
      | description  | Presentation for API     |
      | company      | com1                     |
      | stand        | evt1_s1                  |
      | categories   | Gas;Oil                  |
      | isPremium    | true                     |
    And following "Presentation" exists:
      | identifiedBy | pres2                    |
      | videoUrl     | http://company.api/9     |
      | company      | com2                     |
      | stand        | evt1_s2                  |
      | isPremium    | true                     |
    And following "Presentation" exists:
      | identifiedBy | pres3                    |
      | videoUrl     | http://company.api/2314  |
      | company      | com1                     |
      | stand        | evt2_s1                  |
      | isPremium    | false                    |
    And following "Presentation" exists:
      | identifiedBy | pres4                    |
      | videoUrl     | http://company.api23/76  |
      | company      | com1                     |
      | stand        | evt2_s2                  |
      | isPremium    | false                    |
    And following "Appointment" exists:
      | identifiedBy  | app1                |
      | user          | admin9_api          |
      | presentation  | pres1               |
    And following "Appointment" exists:
      | identifiedBy  | app2                |
      | user          | user9_api           |
      | presentation  | pres1               |
      | isVisited     | true                |
    And following "Appointment" exists:
      | identifiedBy  | app3                |
      | user          | admin9_api          |
      | presentation  | pres2               |
    And following "Appointment" exists:
      | identifiedBy  | app4                |
      | user          | admin9_api          |
      | presentation  | pres3               |
      | isVisited     | true                |
    And following "Appointment" exists:
      | identifiedBy  | app5                |
      | user          | user9_api           |
      | presentation  | pres3               |
    And following "Appointment" exists:
      | identifiedBy  | app6                |
      | user          | user9_api           |
      | presentation  | pres2               |

  Scenario: get list of all appointments
    When I make request "GET" "/api/v1/appointments"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a collection

  Scenario: get one appointment
    When I make request "GET" "/api/v1/appointments/{Appointment_app1}"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "id" field

  Scenario: get appointments list of particular user
    When I make request "GET" "/api/v1/appointments?user={User_user9_api}"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a collection
    And all response collection items should have nested field "_links->user->id" with value "{User_user9_api}"

  Scenario: get appointments list of particular event
    When I make request "GET" "/api/v1/appointments?event={Event_evt1}"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a collection
    And all response collection items should have nested field "_links->event->id" with value "{Event_evt1}"

  Scenario: get appointments list of particular presentation
    When I make request "GET" "/api/v1/appointments?presentation={Presentation_pres2}"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a collection
    And all response collection items should have nested field "_links->presentation->id" with value "{Presentation_pres2}"

  Scenario: create appointment
    Given I am authorized client with username "admin" and password "admin"
    When I make request "POST" "/api/v1/appointments" with parameter-bag params:
      | user          | User_admin9_api      |
      | presentation  | Presentation_pres4  |
      | isVisited     | true                |
    Then "Appointment" should be created with "presentation.id" set to "{Presentation_pres4}"
    And the response status code should be 201
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "id" field

  Scenario: update appointment
    Given I am authorized client with username "admin" and password "admin"
    When I make request "PUT" "/api/v1/appointments/{Appointment_app4}" with parameter-bag params:
      | user          | User_admin9_api      |
      | presentation  | Presentation_pres3  |
      | isVisited     | false               |
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "id" field with value "{Appointment_app4}"
    And the repsonse JSON should have "is_visited" field set to "false"

  Scenario: delete appointment
    Given I am authorized client with username "admin" and password "admin"
    When I make request "DELETE" "/api/v1/appointments/{Appointment_last_created}"
    Then the response status code should be 204
    And I make request "HEAD" "/api/v1/appointments/{Appointment_last_created}"
    And the response status code should be 404

  Scenario Outline: do not create appointment when empty or invalid param
    Given I am authorized client with username "admin" and password "admin"
    When I make request "POST" "/api/v1/appointments" with parameter-bag params:
      | user          | <user>           |
      | event         | <event>          |
      | presentation  | <presentation>   |
      | isVisited     | <isVisited>      |
    Then the response status code should be 400
    And the response should be JSON
    And the repsonse JSON should have "error" field

    Examples:
    | user                 | event               | presentation                | isVisited |
    | User_user9_api       |                     |                             | true      |
    | User_user9_api       |                     | Presentation_not_existing   | true      |
    |                      |                     | Presentation_pres4          | true      |
    | User_not_existing    |                     | Presentation_pres4          | false     |
    | User_user9_api       | Event_not_existing  | Presentation_pres4          | false     |
    | User_user9_api       | Event_evt1          | Presentation_pres4          | false     |

  Scenario: cannot create same appointment twice
    Given I am authorized client with username "admin" and password "admin"
    And following "Appointment" exists:
      | identifiedBy  | app6                 |
      | user          | user9_api            |
      | presentation  | pres2                |
    When I make request "POST" "/api/v1/appointments" with parameter-bag params:
      | user          | User_user9_api       |
      | presentation  | Presentation_pres2   |
    Then the response status code should be 409

  Scenario: cannot create appointment without user context
    When I make request "POST" "/api/v1/appointments"
    Then the response status code should be 403

  Scenario: cannot update appointment without user context
    When I make request "PUT" "/api/v1/appointments/{Appointment_app2}"
    Then the response status code should be 403

  Scenario: cannot delete appointment without user context
    When I make request "DELETE" "/api/v1/appointments/{Appointment_app2}"
    Then the response status code should be 403

  Scenario: user without admin priviledges cannot update appointment of other user
    Given I am authorized client with username "user" and password "user"
    When I make request "PUT" "/api/v1/appointments/{Appointment_app2}"
    Then the response status code should be 403
    And the response should contain "Cannot affect not your appointment"

  Scenario: user without admin priviledges cannot delete appointment of other user
    Given I am authorized client with username "user" and password "user"
    When I make request "DELETE" "/api/v1/appointments/{Appointment_app2}"
    Then the response status code should be 403
    And the response should contain "Cannot affect not your appointment"


