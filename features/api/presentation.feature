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
    And following "Company" exists:
      | identifiedBy  | Company_Api_23                |
      | name          | Company 23                    |
      | websiteUrl    | http://company.api23          |
      | logoUrl       | http://company.api23/logo.png |
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
      | hall          | G               |
      | event         | Event_Api_1     |
      | company       | Company_Api     |
    And following "Stand" exists:
      | identifiedBy  | EvtApi1_F_1335  |
      | number        | 1335            |
      | hall          | G               |
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
      | name         | name of 1st pres             |
      | description  | Presentation for API         |
      | company      | Company_Api                  |
      | stand        | EvtApi1_F_1332               |
      | categories   | Gas;Oil                      |
      | isPremium    | true                         |
    And following "Presentation" exists:
      | identifiedBy | company_api_2                |
      | videoUrl     | http://company.api/2         |
      | description  | descr to search              |
      | name         | name of 2nd pres             |
      | company      | Company_Api                  |
      | stand        | EvtApi1_F_1333               |
      | isPremium    | true                         |
    And following "Presentation" exists:
      | identifiedBy | company_api_free             |
      | videoUrl     | http://company.api/free      |
      | name         | name of free pres            |
      | company      | Company_Api_23               |
      | stand        | EvtApi1_F_1334               |
      | isPremium    | false                        |
    And following "Presentation" exists:
      | identifiedBy | company_api_3                |
      | videoUrl     | http://company.api23/23      |
      | name         | name of 4th pres             |
      | company      | Company_Api                  |
      | stand        | EvtApi2_A_1                  |
      | isPremium    | false                        |

  Scenario: get list of all presentations
    When I make request "GET" "/api/v1/presentations"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the repsonse JSON should have "total_count" field
    And the repsonse JSON should have "current_page_number" field
    And the response JSON "items" field should be a collection

  Scenario: get list of all presentations, including stands information
    When I make request "GET" "/api/v1/presentations?include[]=stand"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And all nested "items" collection items should have "stand" field

  Scenario: get list of all presentations, including companies information
    When I make request "GET" "/api/v1/presentations?include[]=company"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And all nested "items" collection items should have "company" field

  Scenario: get list of all presentations, including stands and companies information
    When I make request "GET" "/api/v1/presentations?include[]=stand&include[]=company"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And all nested "items" collection items should have "stand" field
    And all nested "items" collection items should have "company" field

  Scenario: get list of presentations matching search params: presentation name equals "name of 2nd pres"
    When I make request "GET" "/api/v1/presentations?search[name]=name of 2nd pres"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And all nested "items" collection items should have "name" field with value "name of 2nd pres"

  Scenario: get list of presentations matching search params: presentation name matches substring "name of 2nd"
    When I make request "GET" "/api/v1/presentations?search[name]=name of 2nd"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And all nested "items" collection items should have "name" field with value "name of 2nd pres"

  Scenario: get list of presentations matching search params: presentation description equals "descr to search"
    When I make request "GET" "/api/v1/presentations?search[description]=descr to search"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And all nested "items" collection items should have "description" field with value "descr to search"

  Scenario: get list of presentations matching search params: presentation description matches substring "descr to"
    When I make request "GET" "/api/v1/presentations?search[description]=descr to"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And all nested "items" collection items should have "description" field with value "descr to search"

  Scenario: get list of presentations matching search params: presentation company name equals "Company Api"
    When I make request "GET" "/api/v1/presentations?search[company.name]=Company Api&include[]=company"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And all nested "items" collection items should have nested "company->name" field with value "Company Api"

  Scenario: get list of presentations matching search params: presentation company name matches substring "Company A"
    When I make request "GET" "/api/v1/presentations?search[company.name]=Company A&include[]=company"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And all nested "items" collection items should have nested "company->name" field with value "Company Api"

  Scenario: get list of presentations matching search params: presentation category name equals "Oil"
    When I make request "GET" "/api/v1/presentations?search[category.name]=Oil"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And all nested "items" collection items should have "categories" field

  Scenario: get list of presentations matching search params: presentation category name matches substring "Oi"
    When I make request "GET" "/api/v1/presentations?search[category.name]=Oi"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And all nested "items" collection items should have "categories" field

  Scenario: get list of presentations matching search params: presentation stand hall equals "F"
    When I make request "GET" "/api/v1/presentations?search[stand.hall]=F&include[]=stand"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And all nested "items" collection items should have nested "stand->hall" field with value "F"

  Scenario: get list of presentations matching search params: presentation stand number equals "1333"
    When I make request "GET" "/api/v1/presentations?search[stand.number]=1333&include[]=stand"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And all nested "items" collection items should have nested "stand->number" field with value "1333"

  Scenario: get list of presentations matching search params: presentation stand number equals "1333" and stand hall equals "F"
    When I make request "GET" "/api/v1/presentations?search[stand.number]=1333&search[stand.hall]=F&include[]=stand"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And all nested "items" collection items should have nested "stand->number" field with value "1333"
    And all nested "items" collection items should have nested "stand->hall" field with value "F"

  Scenario: get list of presentations with non-existent search params should return empty collection: presentation name equals "not-existing"
    When I make request "GET" "/api/v1/presentations?search[name]=not-existing"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And the repsonse JSON should have "total_count" field with value "0"

  Scenario: get list of presentations with non-existent search params should return empty collection: presentation description equals "not-existing"
    When I make request "GET" "/api/v1/presentations?search[description]=not-existing"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And the repsonse JSON should have "total_count" field with value "0"

  Scenario: get list of presentations with non-existent search params should return empty collection: presentation company name equals "not-existing"
    When I make request "GET" "/api/v1/presentations?search[company.name]=not-existing"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And the repsonse JSON should have "total_count" field with value "0"

  Scenario: get list of presentations with non-existent search params should return empty collection: presentation category name equals "not-existing"
    When I make request "GET" "/api/v1/presentations?search[category.name]=not-existing"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And the repsonse JSON should have "total_count" field with value "0"

  Scenario: get list of presentations with non-existent search params should return empty collection: presentation stand hall equals "not-existing"
    When I make request "GET" "/api/v1/presentations?search[stand.hall]=not-existing"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And the repsonse JSON should have "total_count" field with value "0"

  Scenario: get list of presentations with non-existent search params should return empty collection: presentation stand number equals "not-existing"
    When I make request "GET" "/api/v1/presentations?search[stand.number]=not-existing"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And the repsonse JSON should have "total_count" field with value "0"

  Scenario: get list of presentations with non-existent search params should return empty collection: presentation stand hall equals "F" and stand number equals "not-existing"
    When I make request "GET" "/api/v1/presentations?search[stand.number]=not-existing&search[stand.hall]=F"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And the repsonse JSON should have "total_count" field with value "0"

  Scenario: get list of presentations with non-existent search params should return empty collection: presentation stand hall equals "not-existing" and stand number equals "1333"
    When I make request "GET" "/api/v1/presentations?search[stand.hall]=not-existing&search[stand.number]=1333"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And the repsonse JSON should have "total_count" field with value "0"

  Scenario: get one presentation
    When I make request "GET" "/api/v1/presentations/{Presentation_company_api_prezi}"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "id" field
    And the repsonse JSON should have "description" field with value "Presentation for API"
    And the repsonse JSON should have "video_url" field with value "http://company.api/prezi"
    And the repsonse JSON should have "name" field with value "name of 1st pres"

  Scenario: get list of particular event presentations
    When I make request "GET" "/api/v1/events/{Event_Event_Api_1}/presentations"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field

  Scenario: get list of particular event presentations, including stands information
    When I make request "GET" "/api/v1/events/{Event_Event_Api_1}/presentations?include[]=stand"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And all nested "items" collection items should have "stand" field

  Scenario: get list of particular event presentations, including companies information
    When I make request "GET" "/api/v1/events/{Event_Event_Api_1}/presentations?include[]=company"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And all nested "items" collection items should have "company" field

  Scenario: get list of particular event presentations, including stands and companies information
    When I make request "GET" "/api/v1/events/{Event_Event_Api_1}/presentations?include[]=stand&include[]=company"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And all nested "items" collection items should have "stand" field
    And all nested "items" collection items should have "company" field

  Scenario: get list of particular event presentations matching search params: presentation name equals "name of 2nd pres"
    When I make request "GET" "/api/v1/events/{Event_Event_Api_1}/presentations?search[name]=name of 2nd pres"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And all nested "items" collection items should have "name" field with value "name of 2nd pres"

  Scenario: get list of particular event presentations matching search params: presentation name matches substring "name of 2n"
    When I make request "GET" "/api/v1/events/{Event_Event_Api_1}/presentations?search[name]=name of 2n"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And all nested "items" collection items should have "name" field with value "name of 2nd pres"

  Scenario: get list of particular event presentations matching search params: presentation description equals "descr to search"
    When I make request "GET" "/api/v1/events/{Event_Event_Api_1}/presentations?search[description]=descr to search"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And all nested "items" collection items should have "description" field with value "descr to search"

  Scenario: get list of particular event presentations matching search params: presentation description matches substring "descr to"
    When I make request "GET" "/api/v1/events/{Event_Event_Api_1}/presentations?search[description]=descr to"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And all nested "items" collection items should have "description" field with value "descr to search"

  Scenario: get list of particular event presentations matching search params: presentation company name equals "Company Api"
    When I make request "GET" "/api/v1/events/{Event_Event_Api_1}/presentations?search[company.name]=Company Api&include[]=company"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And all nested "items" collection items should have nested "company->name" field with value "Company Api"

  Scenario: get list of particular event presentations matching search params: presentation company name matches substring "Company A"
    When I make request "GET" "/api/v1/events/{Event_Event_Api_1}/presentations?search[company.name]=Company A&include[]=company"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And all nested "items" collection items should have nested "company->name" field with value "Company Api"

  Scenario: get list of particular event presentations matching search params: presentation category name equals "Oil"
    When I make request "GET" "/api/v1/events/{Event_Event_Api_1}/presentations?search[category.name]=Oil"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And all nested "items" collection items should have "categories" field

  Scenario: get list of particular event presentations matching search params: presentation category name matches substring "Oi"
    When I make request "GET" "/api/v1/events/{Event_Event_Api_1}/presentations?search[category.name]=Oi"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And all nested "items" collection items should have "categories" field

  Scenario: get list of particular event presentations matching search params: presentation stand hall equals "F"
    When I make request "GET" "/api/v1/events/{Event_Event_Api_1}/presentations?search[stand.hall]=F&include[]=stand"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And all nested "items" collection items should have nested "stand->hall" field with value "F"

  Scenario: get list of particular event presentations matching search params: presentation stand number equals "1333"
    When I make request "GET" "/api/v1/events/{Event_Event_Api_1}/presentations?search[stand.number]=1333&include[]=stand"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And all nested "items" collection items should have nested "stand->number" field with value "1333"

  Scenario: get list of particular event presentations matching search params: presentation stand number equals "1333" and stand hall equals "F"
    When I make request "GET" "/api/v1/events/{Event_Event_Api_1}/presentations?search[stand.number]=1333&search[stand.hall]=F&include[]=stand"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And all nested "items" collection items should have nested "stand->number" field with value "1333"
    And all nested "items" collection items should have nested "stand->hall" field with value "F"

  Scenario: get list of particular event presentations with non-existent search params should return empty collection: presentation name equals "not-existing"
    When I make request "GET" "/api/v1/events/{Event_Event_Api_1}/presentations?search[name]=not-existing"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And the repsonse JSON should have "total_count" field with value "0"

  Scenario: get list of particular event presentations with non-existent search params should return empty collection: presentation description equals "not-existing"
    When I make request "GET" "/api/v1/events/{Event_Event_Api_1}/presentations?search[description]=not-existing"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And the repsonse JSON should have "total_count" field with value "0"

  Scenario: get list of particular event presentations with non-existent search params should return empty collection: presentation company name equals "not-existing"
    When I make request "GET" "/api/v1/events/{Event_Event_Api_1}/presentations?search[company.name]=not-existing"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And the repsonse JSON should have "total_count" field with value "0"

  Scenario: get list of particular event presentations with non-existent search params should return empty collection: presentation category name equals "not-existing"
    When I make request "GET" "/api/v1/events/{Event_Event_Api_1}/presentations?search[category.name]=not-existing"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And the repsonse JSON should have "total_count" field with value "0"

  Scenario: get list of particular event presentations with non-existent search params should return empty collection: presentation stand hall equals "not-existing"
    When I make request "GET" "/api/v1/events/{Event_Event_Api_1}/presentations?search[stand.hall]=not-existing"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And the repsonse JSON should have "total_count" field with value "0"

  Scenario: get list of particular event presentations with non-existent search params should return empty collection: presentation stand number equals "not-existing"
    When I make request "GET" "/api/v1/events/{Event_Event_Api_1}/presentations?search[stand.number]=not-existing"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And the repsonse JSON should have "total_count" field with value "0"

  Scenario: get list of particular event presentations with non-existent search params should return empty collection: presentation stand hall equals "F" and stand number equals "not-existing"
    When I make request "GET" "/api/v1/events/{Event_Event_Api_1}/presentations?search[stand.number]=not-existing&search[stand.hall]=F"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And the repsonse JSON should have "total_count" field with value "0"

  Scenario: get list of particular event presentations with non-existent search params should return empty collection: presentation stand hall equals "not-existing" and stand number equals "1333"
    When I make request "GET" "/api/v1/events/{Event_Event_Api_1}/presentations?search[stand.hall]=not-existing&search[stand.number]=1333"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And the repsonse JSON should have "total_count" field with value "0"

  Scenario: create presentation
    Given I am authorized client with username "admin" and password "admin"
    When I make request "POST" "/api/v1/presentations" with parameter-bag params:
      | videoUrl        | http://show/me        |
      | name            | name of show/me       |
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
    And the repsonse JSON should have "name" field with value "name of show/me"

  Scenario: update presentation
    Given I am authorized client with username "admin" and password "admin"
    When I make request "PUT" "/api/v1/presentations/{Presentation_last_created}" with parameter-bag params:
      | videoUrl        | http://show/me/2      |
      | description     | Some description 2    |
      | name            | new name of show/me   |
      | company         | Company_Company_Api   |
      | stand           | Stand_EvtApi1_F_1335  |
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "id" field
    And the repsonse JSON should have "video_url" field with value "http://show/me/2"
    And the repsonse JSON should have "description" field with value "Some description 2"
    And the repsonse JSON should have "name" field with value "new name of show/me"

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
      | name            | <name>          |
      | description     | <description>   |
      | company         | <company>       |
      | stand           | <stand>         |
    Then the response status code should be 400
    And the response should be JSON
    And the repsonse JSON should have "error" field

    Examples:
    | videoUrl         | name  | description        | company             | stand                |
    |                  | P     | Some description 2 | Company_Company_Api | Stand_EvtApi1_F_1334 |
    | http://show/me/2 | P     | Some description 2 |                     | Stand_EvtApi1_F_1334 |
    | http://show/me/2 | P     | Some description 2 | Company_Company_Api |                      |
    | http://show/me/2 | P     | Some description 2 | not-existing        | Stand_EvtApi1_F_1334 |
    | http://show/me/2 | P     | Some description 2 | Company_Company_Api | not-existing         |
    | http://show/me/2 |       | Some description 2 | Company_Company_Api | Stand_EvtApi1_F_1334        |

  Scenario: cannot create presentation on occupied stand
    Given I am authorized client with username "admin" and password "admin"
    When I make request "POST" "/api/v1/presentations" with parameter-bag params:
      | videoUrl        | http://show/me/two    |
      | name            | name of show/me/two   |
      | description     | Some description two  |
      | company         | Company_Company_Api   |
      | stand           | Stand_EvtApi1_F_1334  |
    Then the response status code should be 400
    And the response should contain "already has presentation"

  Scenario: cannot update presentation to occupied stand
    Given I am authorized client with username "admin" and password "admin"
    When I make request "PUT" "/api/v1/presentations/{Presentation_company_api_prezi}" with parameter-bag params:
      | videoUrl        | http://show/me/2      |
      | name            | name of show/me/2     |
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


