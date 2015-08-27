Feature: getting stands through API
  In order to get information about event stands
  As an authorized client application
  I want to be able to retrieve stand list

  Background:
    Given I am authorized client
    And following "Event" exists:
      | identifiedBy  | Event_Api_3 |
      | name          | Event Api 3 |
      | location      | Honningsvag |
      | dateStart     | 2014-02-02  |
      | dateEnd       | 2014-02-04  |

    And following "Company" exists:
      | identifiedBy  | Company_Api                 |
      | name          | Company Api                 |
      | websiteUrl    | http://company.api          |
      | logoUrl       | http://company.api/logo.png |

    And following "Stand" exists:
      | identifiedBy  | EvtApi1_F_1332  |
      | number        | 1332            |
      | hall          | F               |
      | event         | Event_Api_3     |
      | company       | Company_Api     |

    And following "Stand" exists:
      | identifiedBy  | EvtApi1_1234    |
      | number        | 1234            |
      | event         | Event_Api_3     |

  Scenario: get list of all stands
    When I make request "GET" "/api/v1/stands"
    Then the response status code should be 200
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the repsonse JSON should have "total_count" field
    And the repsonse JSON should have "current_page_number" field
    And the response JSON "items" field should be a collection

  Scenario: get one stand
    When I make request "GET" "/api/v1/stands/{Stand_EvtApi1_F_1332}"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "id" field
    And the repsonse JSON should have "number" field with value "1332"
    And the repsonse JSON should have "hall" field with value "F"

  Scenario: create stand
    Given I am authorized client with username "admin" and password "admin"
    When I make request "POST" "/api/v1/stands" with parameter-bag params:
      | number          | 578                   |
      | hall            | A                     |
      | company         | Company_Company_Api   |
      | event           | Event_Event_Api_3     |
    Then "Stand" should be created with "number" set to "578"
    And the response status code should be 201
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "id" field
    And the repsonse JSON should have "number" field with value "578"
    And the repsonse JSON should have "hall" field with value "A"

  Scenario: update stand
    Given I am authorized client with username "admin" and password "admin"
    When I make request "PUT" "/api/v1/stands/{Stand_last_created}" with parameter-bag params:
      | number          | 678                   |
      | hall            | B                     |
      | company         | Company_Company_Api   |
      | event           | Event_Event_Api_3     |
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "id" field
    And the repsonse JSON should have "number" field with value "678"
    And the repsonse JSON should have "hall" field with value "B"

  Scenario: delete stand
    Given I am authorized client with username "admin" and password "admin"
    When I make request "DELETE" "/api/v1/stands/{Stand_last_created}"
    Then the response status code should be 204
    And I make request "HEAD" "/api/v1/stands/{Stand_last_created}"
    And the response status code should be 404

  Scenario Outline: do not create stand when empty or invalid param
    Given I am authorized client with username "admin" and password "admin"
    When I make request "POST" "/api/v1/stands" with parameter-bag params:
      | number        | <number>    |
      | hall          | <hall>      |
      | event         | <event>     |
      | company       | <company>   |
    Then the response status code should be 400
    And the response should be JSON
    And the repsonse JSON should have "error" field

  Examples:
    | number          | hall            | event             | company           |
    |                 | F               | Event_Api_3       | Company_Api       |
    | 1332            | F               |                   | Company_Api       |
    | 1332            | F               | not-existing      | Company_Api       |
    | 1332            | F               | Event_Api_3       | not-existing      |

  Scenario: cannot create stand without user context
    When I make request "POST" "/api/v1/stands"
    Then the response status code should be 403

  Scenario: cannot update stand without user context
    When I make request "PUT" "/api/v1/stands/{Stand_EvtApi1_F_1332}"
    Then the response status code should be 403

  Scenario: cannot delete stand without user context
    When I make request "DELETE" "/api/v1/stands/{Stand_EvtApi1_F_1332}"
    Then the response status code should be 403

  Scenario: cannot create stand without admin priviledges
    Given I am authorized client with username "user" and password "user"
    When I make request "POST" "/api/v1/stands"
    Then the response status code should be 403

  Scenario: cannot update stand without admin priviledges
    Given I am authorized client with username "user" and password "user"
    When I make request "PUT" "/api/v1/stands/{Stand_EvtApi1_F_1332}"
    Then the response status code should be 403

  Scenario: cannot delete stand without admin priviledges
    Given I am authorized client with username "user" and password "user"
    When I make request "DELETE" "/api/v1/stands/{Stand_EvtApi1_F_1332}"
    Then the response status code should be 403
