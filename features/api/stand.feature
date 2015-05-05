Feature: getting stands through API
  In order to get information about event stands
  As an authorized client application
  I want to be able to retrieve stand list

  Background:
    Given I am authorized client
    And following "Event" exists:
      | identifiedBy  | Event Api 1 |
      | name          | Event Api 1 |
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
      | event         | Event Api 1     |
      | company       | Company Api     |

    And following "Stand" exists:
      | identifiedBy  | EvtApi1_1234    |
      | number        | 1234            |
      | event         | Event Api 1     |

  Scenario: get list of all stands
    When I make request "GET" "/api/v1/stands"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a collection

  Scenario: get one stand
    When I make request "GET" "/api/v1/stands/{Stand:EvtApi1_F_1332}"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "id" field
    And the repsonse JSON should have "number" field with value "1332"
    And the repsonse JSON should have "hall" field with value "F"

