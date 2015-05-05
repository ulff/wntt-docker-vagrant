Feature: getting categories through API
  In order to get information about categories
  As an authorized client application
  I want to be able to retrieve categories list

  Background:
    Given I am authorized client
    And following "Category" exists:
      | identifiedBy | Gas |
      | name         | Gas |

  Scenario: get list of all categories
    When I make request "GET" "/api/v1/categories"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a collection

  Scenario: get one category
    When I make request "GET" "/api/v1/categories/{Category:Gas}"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "id" field
    And the repsonse JSON should have "name" field with value "Gas"