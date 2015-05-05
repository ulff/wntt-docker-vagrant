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
    When I make request "GET" "/api/v1/events/{Event:Event Api 1}"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "id" field
    And the repsonse JSON should have "name" field with value "Event Api 1"
    And the repsonse JSON should have "location" field with value "Honningsvag"

