Feature: getting stands through API
  In order to get information about event stands
  As an authorized client application
  I want to be able to retrieve stand list

  Background:
    Given I am authorized client
    And following "Event" exists:
      | identifiedBy  | Event Api 3 |
      | name          | Event Api 3 |
      | location      | Honningsvag |
      | dateStart     | 2014-02-02  |
      | dateEnd       | 2014-02-04  |

    And following "Company" exists:
      | identifiedBy  | Company Api                 |
      | name          | Company Api                 |
      | websiteUrl    | http://company.api          |
      | logoUrl       | http://company.api/logo.png |

    And following "Stand" exists:
      | identifiedBy  | EvtApi1_F_1332  |
      | number        | 1332            |
      | hall          | F               |
      | event         | Event Api 3     |
      | company       | Company Api     |

    And following "Stand" exists:
      | identifiedBy  | EvtApi1_1234    |
      | number        | 1234            |
      | event         | Event Api 3     |

  Scenario: get list of all stands
    When I make request "GET" "/api/v1/stands"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a collection

  Scenario: get one stand
    When I make request "GET" "/api/v1/stands/{Stand_EvtApi1_F_1332}"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "id" field
    And the repsonse JSON should have "number" field with value "1332"
    And the repsonse JSON should have "hall" field with value "F"

  Scenario: create stand
    When I make request "POST" "/api/v1/stands" with parameter-bag params:
      | number          | 578                   |
      | hall            | A                     |
      | company         | Company_Company Api   |
      | event           | Event_Event Api 3     |
    Then "Stand" should be created with "number" set to "578"
    And the response status code should be 201
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "id" field
    And the repsonse JSON should have "number" field with value "578"
    And the repsonse JSON should have "hall" field with value "A"

  Scenario: update stand
    When I make request "PUT" "/api/v1/stands/{Stand_last_created}" with parameter-bag params:
      | number          | 678                   |
      | hall            | B                     |
      | company         | Company_Company Api   |
      | event           | Event_Event Api 3     |
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "id" field
    And the repsonse JSON should have "number" field with value "678"
    And the repsonse JSON should have "hall" field with value "B"

  Scenario: delete stand
    When I make request "DELETE" "/api/v1/stands/{Stand_last_created}"
    Then the response status code should be 204
    And I make request "HEAD" "/api/v1/stands/{Stand_last_created}"
    And the response status code should be 404

  Scenario Outline: do not create stand when empty or invalid param
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
    |                 | F               | Event Api 3       | Company Api       |
    | 1332            | F               |                   | Company Api       |
    | 1332            | F               | not-existing      | Company Api       |
    | 1332            | F               | Event Api 3       | not-existing      |

