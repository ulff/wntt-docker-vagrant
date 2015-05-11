Feature: getting events through API
  In order to get information about events
  As an authorized client application
  I want to be able to retrieve event list

  Background:
    Given I am authorized client
    And following "Event" exists:
      | identifiedBy  | Event Api 1 |
      | name          | Event Api 1 |
      | location      | Honningsvag |
      | dateStart     | 2014-02-02  |
      | dateEnd       | 2014-02-04  |

    And following "Event" exists:
      | identifiedBy  | Event Api 2 |
      | name          | Event Api 2 |
      | location      | Tromso      |
      | dateStart     | 2015-07-31  |
      | dateEnd       | 2015-08-23  |

  Scenario: get list of all events
    When I make request "GET" "/api/v1/events"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a collection

  Scenario: get one event
    When I make request "GET" "/api/v1/events/{Event_Event Api 1}"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "id" field
    And the repsonse JSON should have "name" field with value "Event Api 1"
    And the repsonse JSON should have "location" field with value "Honningsvag"
    And the repsonse JSON should have "date_start" field with value "2014-02-02T00:00:00+0000"
    And the repsonse JSON should have "date_end" field with value "2014-02-04T00:00:00+0000"
    
  Scenario: create event
    When I make request "POST" "/api/v1/events" with params:
      | name        | Created event name  |
      | location    | Narvik              |
      | dateStart   | 2015-02-23          |
      | dateEnd     | 2015-02-24          |
    Then "Event" should be created with "name" set to "Created event name"
    And the response status code should be 201
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "id" field
    And the repsonse JSON should have "name" field with value "Created event name"
    And the repsonse JSON should have "location" field with value "Narvik"
    And the repsonse JSON should have "date_start" field with value "2015-02-23T00:00:00+0000"
    And the repsonse JSON should have "date_end" field with value "2015-02-24T00:00:00+0000"

  Scenario: update event
    When I make request "PUT" "/api/v1/events/{Event_last_created}" with params:
      | name        | Updated event name  |
      | location    | Stavanger           |
      | dateStart   | 2015-02-26          |
      | dateEnd     | 2015-02-27          |
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "id" field
    And the repsonse JSON should have "name" field with value "Updated event name"
    And the repsonse JSON should have "location" field with value "Stavanger"
    And the repsonse JSON should have "date_start" field with value "2015-02-26T00:00:00+0000"
    And the repsonse JSON should have "date_end" field with value "2015-02-27T00:00:00+0000"

  Scenario: delete event
    When I make request "DELETE" "/api/v1/events/{Event_last_created}"
    Then the response status code should be 204
    And I make request "HEAD" "/api/v1/events/{Event_last_created}"
    And the response status code should be 404

  Scenario: do not create event when empty param name
    When I make request "POST" "/api/v1/events" with params:
      | location    | Tromso              |
      | dateStart   | 2015-02-23          |
      | dateEnd     | 2015-02-24          |
    Then the response status code should be 400
    And the response should be JSON
    And the repsonse JSON should have "error" field

  Scenario: do not create event when empty param dateStart
    When I make request "POST" "/api/v1/events" with params:
      | name        | Exhibition name     |
      | location    | Tromso              |
      | dateEnd     | 2015-02-24          |
    Then the response status code should be 400
    And the response should be JSON
    And the repsonse JSON should have "error" field

  Scenario: do not create event when empty param dateEnd
    When I make request "POST" "/api/v1/events" with params:
      | name        | Exhibition name     |
      | location    | Tromso              |
      | dateStart     | 2015-02-24          |
    Then the response status code should be 400
    And the response should be JSON
    And the repsonse JSON should have "error" field




