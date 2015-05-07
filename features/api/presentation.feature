Feature: getting presentations through API
  In order to get information about presentations
  As an authorized client application
  I want to be able to retrieve presentation list

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

    And following "Category" exists:
      | identifiedBy | Gas |
      | name         | Gas |

    And following "Category" exists:
      | identifiedBy | Oil |
      | name         | Oil |

    And following "Presentation" exists:
      | identifiedBy | company api prezi            |
      | videoUrl     | http://company.api/prezi     |
      | description  | Presentation for API testing |
      | company      | Company Api                  |
      | stand        | EvtApi1_F_1332               |
      | categories   | Gas;Oil                      |


  Scenario: get list of all presentations
    When I make request "GET" "/api/v1/presentations"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a collection

  Scenario: get one presentation
    When I make request "GET" "/api/v1/presentations/{Presentation:company api prezi}"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "id" field
    And the repsonse JSON should have "description" field with value "Presentation for API testing"
    And the repsonse JSON should have "video_url" field with value "http://company.api/prezi"

