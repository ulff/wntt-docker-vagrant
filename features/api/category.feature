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
    When I make request "GET" "/api/v1/categories/{Category_Gas}"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "id" field
    And the repsonse JSON should have "name" field with value "Gas"

  Scenario: create category
    Given I am authorized client with username "admin" and password "admin"
    When I make request "POST" "/api/v1/categories" with params:
      | name        | Created category  |
    Then "Category" should be created with "name" set to "Created category"
    And the response status code should be 201
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "id" field
    And the repsonse JSON should have "name" field with value "Created category"

  Scenario: update category
    Given I am authorized client with username "admin" and password "admin"
    When I make request "PUT" "/api/v1/categories/{Category_last_created}" with params:
      | name        | Updated category  |
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "id" field
    And the repsonse JSON should have "name" field with value "Updated category"

  Scenario: delete category
    Given I am authorized client with username "admin" and password "admin"
    When I make request "DELETE" "/api/v1/categories/{Category_last_created}"
    Then the response status code should be 204
    And I make request "HEAD" "/api/v1/categories/{Category_last_created}"
    And the response status code should be 404

  Scenario: do not create category when empty param name
    Given I am authorized client with username "admin" and password "admin"
    When I make request "POST" "/api/v1/categories"
    Then the response status code should be 400
    And the response should be JSON
    And the repsonse JSON should have "error" field

  Scenario: cannot create category without user context
    When I make request "POST" "/api/v1/categories"
    Then the response status code should be 403

  Scenario: cannot update category without user context
    When I make request "PUT" "/api/v1/categories/{Category_Gas}"
    Then the response status code should be 403

  Scenario: cannot delete category without user context
    When I make request "DELETE" "/api/v1/categories/{Category_Gas}"
    Then the response status code should be 403

  Scenario: cannot update category without admin priviledges
    Given I am authorized client with username "user" and password "user"
    When I make request "PUT" "/api/v1/categories/{Category_Gas}"
    Then the response status code should be 403

  Scenario: cannot delete category without admin priviledges
    Given I am authorized client with username "user" and password "user"
    When I make request "DELETE" "/api/v1/categories/{Category_Gas}"
    Then the response status code should be 403