Feature: managing presentations through API
  In order to manage presentations
  As an authorized client application
  I want to be able to do all operations on presentations

  Background:
    Given I am authorized client
    And following "Event" exists:
      | identifiedBy  | Event_Api_1 |
      | name          | Event Api 1 |
      | location      | Honningsvag |
      | dateStart     | 2014-02-02  |
      | dateEnd       | 2014-02-04  |
    And following "Event" exists:
      | identifiedBy  | Event_Api_2 |
      | name          | Event Api 2 |
      | location      | Narvik      |
      | dateStart     | 2014-03-04  |
      | dateEnd       | 2014-03-07  |
    And following "Company" exists:
      | identifiedBy  | Company_Api                 |
      | name          | Company Api                 |
      | websiteUrl    | http://company.api          |
      | logoUrl       | http://company.api/logo.png |
    And following "Stand" exists:
      | identifiedBy  | EvtApi1_F_1332  |
      | number        | 1332            |
      | hall          | F               |
      | event         | Event_Api_1     |
      | company       | Company_Api     |
    And following "Stand" exists:
      | identifiedBy  | EvtApi1_F_1333  |
      | number        | 1333            |
      | hall          | F               |
      | event         | Event_Api_1     |
      | company       | Company_Api     |
    And following "Stand" exists:
      | identifiedBy  | EvtApi1_F_1334  |
      | number        | 1334            |
      | hall          | F               |
      | event         | Event_Api_1     |
      | company       | Company_Api     |
    And following "Stand" exists:
      | identifiedBy  | EvtApi1_F_1335  |
      | number        | 1335            |
      | hall          | F               |
      | event         | Event_Api_1     |
      | company       | Company_Api     |
    And following "Stand" exists:
      | identifiedBy  | EvtApi2_A_1     |
      | number        | 1               |
      | hall          | A               |
      | event         | Event_Api_2     |
      | company       | Company_Api     |
    And following "Category" exists:
      | identifiedBy | Gas |
      | name         | Gas |
    And following "Category" exists:
      | identifiedBy | Oil |
      | name         | Oil |
    And following "Presentation" exists:
      | identifiedBy | company_api_prezi            |
      | videoUrl     | http://company.api/prezi     |
      | description  | Presentation for API         |
      | company      | Company_Api                  |
      | stand        | EvtApi1_F_1332               |
      | categories   | Gas;Oil                      |
      | isPremium    | true                         |
    And following "Presentation" exists:
      | identifiedBy | company_api_2                |
      | videoUrl     | http://company.api/2         |
      | company      | Company_Api                  |
      | stand        | EvtApi1_F_1333               |
      | isPremium    | true                         |
    And following "Presentation" exists:
      | identifiedBy | company_api_free             |
      | videoUrl     | http://company.api/free      |
      | company      | Company_Api                  |
      | stand        | EvtApi1_F_1334               |
      | isPremium    | false                        |
    And following "Presentation" exists:
      | identifiedBy | company_api_3                |
      | videoUrl     | http://company.api23/23      |
      | company      | Company_Api                  |
      | stand        | EvtApi2_A_1                  |
      | isPremium    | false                        |

  Scenario: get list of all presentations
    When I make request "GET" "/api/v1/presentations"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a collection

  Scenario: get one presentation
    When I make request "GET" "/api/v1/presentations/{Presentation_company_api_prezi}"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "id" field
    And the repsonse JSON should have "description" field with value "Presentation for API"
    And the repsonse JSON should have "video_url" field with value "http://company.api/prezi"

  Scenario: get list of premium presentations
    When I make request "GET" "/api/v1/presentations?type=premium"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a collection
    And all response collection items should have "is_premium" field set to "true"

  Scenario: get list of free presentations
    When I make request "GET" "/api/v1/presentations?type=free"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a collection
    And all response collection items should have "is_premium" field set to "false"

  Scenario: get list of particular event presentations
    When I make request "GET" "/api/v1/events/{Event_Event_Api_1}/presentations"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a collection

  Scenario: create presentation
    Given I am authorized client with username "admin" and password "admin"
    When I make request "POST" "/api/v1/presentations" with parameter-bag params:
      | videoUrl        | http://show/me        |
      | description     | Some description      |
      | company         | Company_Company_Api   |
      | stand           | Stand_EvtApi1_F_1335  |
    Then "Presentation" should be created with "videoUrl" set to "http://show/me"
    And the response status code should be 201
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "id" field
    And the repsonse JSON should have "video_url" field with value "http://show/me"
    And the repsonse JSON should have "description" field with value "Some description"

  Scenario: update presentation
    Given I am authorized client with username "admin" and password "admin"
    When I make request "PUT" "/api/v1/presentations/{Presentation_last_created}" with parameter-bag params:
      | videoUrl        | http://show/me/2      |
      | description     | Some description 2    |
      | company         | Company_Company_Api   |
      | stand           | Stand_EvtApi1_F_1335  |
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "id" field
    And the repsonse JSON should have "video_url" field with value "http://show/me/2"
    And the repsonse JSON should have "description" field with value "Some description 2"

  Scenario: delete presentation
    Given I am authorized client with username "admin" and password "admin"
    When I make request "DELETE" "/api/v1/presentations/{Presentation_last_created}"
    Then the response status code should be 204
    And I make request "HEAD" "/api/v1/presentations/{Presentation_last_created}"
    And the response status code should be 404

  Scenario Outline: do not create presentation when empty or invalid param
    Given I am authorized client with username "admin" and password "admin"
    When I make request "POST" "/api/v1/presentations" with parameter-bag params:
      | videoUrl        | <videoUrl>      |
      | description     | <description>   |
      | company         | <company>       |
      | stand           | <stand>         |
    Then the response status code should be 400
    And the response should be JSON
    And the repsonse JSON should have "error" field

    Examples:
    | videoUrl         | description        | company             | stand                |
    |                  | Some description 2 | Company_Company_Api | Stand_EvtApi1_F_1334 |
    | http://show/me/2 | Some description 2 |                     | Stand_EvtApi1_F_1334 |
    | http://show/me/2 | Some description 2 | Company_Company_Api |                      |
    | http://show/me/2 | Some description 2 | not-existing        | Stand_EvtApi1_F_1334 |
    | http://show/me/2 | Some description 2 | Company_Company_Api | not-existing         |

  Scenario: cannot create presentation on occupied stand
    Given I am authorized client with username "admin" and password "admin"
    When I make request "POST" "/api/v1/presentations" with parameter-bag params:
      | videoUrl        | http://show/me/two    |
      | description     | Some description two  |
      | company         | Company_Company_Api   |
      | stand           | Stand_EvtApi1_F_1334  |
    Then the response status code should be 400
    And the response should contain "already has presentation"

  Scenario: cannot update presentation to occupied stand
    Given I am authorized client with username "admin" and password "admin"
    When I make request "PUT" "/api/v1/presentations/{Presentation_company_api_prezi}" with parameter-bag params:
      | videoUrl        | http://show/me/2      |
      | description     | Some description 2    |
      | company         | Company_Company_Api   |
      | stand           | Stand_EvtApi1_F_1334  |
    Then the response status code should be 400
    And the response should contain "already has presentation"

  Scenario: cannot create presentation without user context
    When I make request "POST" "/api/v1/presentations"
    Then the response status code should be 403

  Scenario: cannot update presentation without user context
    When I make request "PUT" "/api/v1/presentations/{Presentation_company_api_prezi}"
    Then the response status code should be 403

  Scenario: cannot delete presentation without user context
    When I make request "DELETE" "/api/v1/presentations/{Presentation_company_api_prezi}"
    Then the response status code should be 403

  Scenario: user without admin priviledges cannot create presentation owned by not his company
    Given I am authorized client with username "user" and password "user"
    When I make request "POST" "/api/v1/presentations"
    Then the response status code should be 403
    And the response should contain "Cannot affect presentation owned by not your company"

  Scenario: user without admin priviledges cannot update presentation owned by not his company
    Given I am authorized client with username "user" and password "user"
    When I make request "PUT" "/api/v1/presentations/{Presentation_company_api_prezi}"
    Then the response status code should be 403
    And the response should contain "Cannot affect presentation owned by not your company"

  Scenario: user without admin priviledges cannot delete presentation owned by not his company
    Given I am authorized client with username "user" and password "user"
    When I make request "DELETE" "/api/v1/presentations/{Presentation_company_api_prezi}"
    Then the response status code should be 403
    And the response should contain "Cannot affect presentation owned by not your company"


