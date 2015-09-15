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
      | hall         | F                            |
      | number       | 1333                         |
      | description  | Presentation for API         |
      | company      | Company_Api                  |
      | event        | Event_Api_1                  |
      | categories   | Gas;Oil                      |
      | isPremium    | true                         |
    And following "Presentation" exists:
      | identifiedBy | company_api_2                |
      | videoUrl     | http://company.api/2         |
      | description  | descr to search              |
      | name         | name of 2nd pres             |
      | hall         | G                            |
      | number       | 1334                         |
      | company      | Company_Api                  |
      | event        | Event_Api_1                  |
      | isPremium    | true                         |
    And following "Presentation" exists:
      | identifiedBy | company_api_free             |
      | videoUrl     | http://company.api/free      |
      | name         | name of free pres            |
      | hall         | F                            |
      | number       | 1335                         |
      | company      | Company_Api_23               |
      | event        | Event_Api_1                  |
      | isPremium    | false                        |
    And following "Presentation" exists:
      | identifiedBy | company_api_3                |
      | videoUrl     | http://company.api23/23      |
      | name         | name of 4th pres             |
      | hall         | G                            |
      | number       | 1333                         |
      | company      | Company_Api                  |
      | event        | Event_Api_2                  |
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

  Scenario: should allow OPTIONS method
    When I make request "OPTIONS" "/api/v1/presentations/{Presentation_company_api_prezi}"
    Then the response status code should be 200

  Scenario: should return 404 when called OPTIONS method on not existing ID
    When I make request "OPTIONS" "/api/v1/presentations/notexisting"
    Then the response status code should be 404

  Scenario: get list of all presentations, including events information
    When I make request "GET" "/api/v1/presentations?include[]=event"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And all nested "items" collection items should have "event" field

  Scenario: get list of all presentations, including companies information
    When I make request "GET" "/api/v1/presentations?include[]=company"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And all nested "items" collection items should have "company" field

  Scenario: get list of all presentations, including events and companies information
    When I make request "GET" "/api/v1/presentations?include[]=event&include[]=company"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And all nested "items" collection items should have "event" field
    And all nested "items" collection items should have "company" field

  Scenario: get list of presentations filtered by event id
    When I make request "GET" "/api/v1/presentations?event={Event_Event_Api_2}"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And all nested "items" collection items should have nested "_links->event->id" field with value "{Event_Event_Api_2}"

  Scenario: get list of presentations filtered by company id
    When I make request "GET" "/api/v1/presentations?company={Company_Company_Api}"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And all nested "items" collection items should have nested "_links->company->id" field with value "{Company_Company_Api}"

  Scenario: get list of presentations filtered by event id and company id
    When I make request "GET" "/api/v1/presentations?event={Event_Event_Api_1}&company={Company_Company_Api}"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And all nested "items" collection items should have nested "_links->event->id" field with value "{Event_Event_Api_1}"
    And all nested "items" collection items should have nested "_links->company->id" field with value "{Company_Company_Api}"

  Scenario: should return 404 on non-existing event id
    When I make request "GET" "/api/v1/presentations?event=not-existing"
    Then the response status code should be 404

  Scenario: should return 404 on non-existing company id
    When I make request "GET" "/api/v1/presentations?company=not-existing"
    Then the response status code should be 404

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

  Scenario: get list of presentations matching search params: presentation hall equals "F"
    When I make request "GET" "/api/v1/presentations?search[hall]=F"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And all nested "items" collection items should have "hall" field with value "F"

  Scenario: get list of presentations matching search params: presentation number equals "1333"
    When I make request "GET" "/api/v1/presentations?search[number]=1333"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And all nested "items" collection items should have "number" field with value "1333"

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

  Scenario: get list of presentations with non-existent search params should return empty collection: presentation hall equals "not-existing"
    When I make request "GET" "/api/v1/presentations?search[hall]=not-existing"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And the repsonse JSON should have "total_count" field with value "0"

  Scenario: get list of presentations with non-existent search params should return empty collection: presentation number equals "not-existing"
    When I make request "GET" "/api/v1/presentations?search[number]=not-existing"
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
    And all nested "items" collection items should have nested "_links->event->id" field with value "{Event_Event_Api_1}"

  Scenario: get list of particular event presentations, including events information
    When I make request "GET" "/api/v1/events/{Event_Event_Api_1}/presentations?include[]=event"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And all nested "items" collection items should have "event" field

  Scenario: get list of particular event presentations, including companies information
    When I make request "GET" "/api/v1/events/{Event_Event_Api_1}/presentations?include[]=company"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And all nested "items" collection items should have "company" field

  Scenario: get list of particular event presentations, including events and companies information
    When I make request "GET" "/api/v1/events/{Event_Event_Api_1}/presentations?include[]=event&include[]=company"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And all nested "items" collection items should have "event" field
    And all nested "items" collection items should have "company" field

  Scenario: get list of particular event presentations filtered by company id
    When I make request "GET" "/api/v1/events/{Event_Event_Api_1}/presentations?company={Company_Company_Api}"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And all nested "items" collection items should have nested "_links->company->id" field with value "{Company_Company_Api}"
    And all nested "items" collection items should have nested "_links->event->id" field with value "{Event_Event_Api_1}"

  Scenario: should return 404 on non-existing company id
    When I make request "GET" "/api/v1/events/{Event_Event_Api_1}/presentations?company=not-existing"
    Then the response status code should be 404

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

  Scenario: get list of particular event presentations matching search params: presentation hall equals "F"
    When I make request "GET" "/api/v1/events/{Event_Event_Api_1}/presentations?search[hall]=F"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And all nested "items" collection items should have "hall" field with value "F"

  Scenario: get list of particular event presentations matching search params: presentation number equals "1333"
    When I make request "GET" "/api/v1/events/{Event_Event_Api_1}/presentations?search[number]=1333"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And all nested "items" collection items should have "number" field with value "1333"

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

  Scenario: get list of particular event presentations with non-existent search params should return empty collection: presentation hall equals "not-existing"
    When I make request "GET" "/api/v1/events/{Event_Event_Api_1}/presentations?search[hall]=not-existing"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And the repsonse JSON should have "total_count" field with value "0"

  Scenario: get list of particular event presentations with non-existent search params should return empty collection: presentation number equals "not-existing"
    When I make request "GET" "/api/v1/events/{Event_Event_Api_1}/presentations?search[number]=not-existing"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "items" field
    And the response JSON "items" field should be a collection
    And the repsonse JSON should have "total_count" field with value "0"

  Scenario: get distinct halls list
    When I make request "GET" "/api/v1/events/{Event_Event_Api_1}/halls"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a collection
    And the response should contain "F"
    And the response should contain "G"

  Scenario: should return 404 on not existing event
    When I make request "GET" "/api/v1/events/not-existing/halls"
    Then the response status code should be 404

  Scenario: create presentation
    Given I am authorized client with username "admin" and password "admin"
    When I make request "POST" "/api/v1/presentations" with parameter-bag params:
      | videoUrl        | http://show/me        |
      | name            | name of show/me       |
      | description     | Some description      |
      | company         | Company_Company_Api   |
      | event           | Event_Event_Api_1     |
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
      | event           | Event_Event_Api_1     |
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
      | event           | <event>         |
    Then the response status code should be 400
    And the response should be JSON
    And the repsonse JSON should have "error" field

    Examples:
    | videoUrl         | name  | description        | company             | event                |
    |                  | P     | Some description 2 | Company_Company_Api | Event_Event_Api_1 |
    | http://show/me/2 | P     | Some description 2 |                     | Event_Event_Api_1 |
    | http://show/me/2 | P     | Some description 2 | Company_Company_Api |                      |
    | http://show/me/2 | P     | Some description 2 | not-existing        | Event_Event_Api_1 |
    | http://show/me/2 | P     | Some description 2 | Company_Company_Api | not-existing         |
    | http://show/me/2 |       | Some description 2 | Company_Company_Api | Event_Event_Api_1        |

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


