Feature: getting companies through API
  In order to get information about event participants
  As an authorized client application
  I want to be able to retrieve companies list

  Background:
    Given I am authorized client
    And following "Company" exists:
      | identifiedBy  | Company Api                 |
      | name          | Company Api                 |
      | websiteUrl    | http://company.api          |
      | logoUrl       | http://company.api/logo.png |
    And following "Company" exists:
      | identifiedBy  | Company Api 2               |
      | name          | Company Api 2               |

  Scenario: get list of all companies
    When I make request "GET" "/api/v1/companies"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a collection

  Scenario: get one company
    When I make request "GET" "/api/v1/companies/{Company_Company Api}"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "id" field
    And the repsonse JSON should have "name" field with value "Company Api"
    And the repsonse JSON should have "website_url" field with value "http://company.api"
    And the repsonse JSON should have "logo_url" field with value "http://company.api/logo.png"

  Scenario: create company
    Given I am authorized client with username "admin" and password "admin"
    When I make request "POST" "/api/v1/companies" with params:
      | name        | Created company name  |
      | websiteUrl  | http://wuwuwu/        |
      | logoUrl     | http://logogo/        |
    Then "Company" should be created with "name" set to "Created company name"
    And the response status code should be 201
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "id" field
    And the repsonse JSON should have "name" field with value "Created company name"
    And the repsonse JSON should have "website_url" field with value "http://wuwuwu/"
    And the repsonse JSON should have "logo_url" field with value "http://logogo/"

  Scenario: update company
    Given I am authorized client with username "admin" and password "admin"
    When I make request "PUT" "/api/v1/companies/{Company_last_created}" with params:
      | name        | Created company new name  |
      | websiteUrl  | http://wuwuwu/new         |
      | logoUrl     | http://logogo/new         |
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "id" field
    And the repsonse JSON should have "name" field with value "Created company new name"
    And the repsonse JSON should have "website_url" field with value "http://wuwuwu/new"
    And the repsonse JSON should have "logo_url" field with value "http://logogo/new"

  Scenario: delete company
    Given I am authorized client with username "admin" and password "admin"
    When I make request "DELETE" "/api/v1/companies/{Company_last_created}"
    Then the response status code should be 204
    And I make request "HEAD" "/api/v1/companies/{Company_last_created}"
    And the response status code should be 404

  Scenario: do not create company when empty param name
    Given I am authorized client with username "admin" and password "admin"
    When I make request "POST" "/api/v1/companies" with params:
      | websiteUrl  | http://wuwuwu/        |
      | logoUrl     | http://logogo/        |
    Then the response status code should be 400
    And the response should be JSON
    And the repsonse JSON should have "error" field

  Scenario: cannot create company without user context
    When I make request "POST" "/api/v1/companies"
    Then the response status code should be 403

  Scenario: cannot update company without user context
    When I make request "PUT" "/api/v1/companies/{Company_Company Api}"
    Then the response status code should be 403

  Scenario: cannot delete company without user context
    When I make request "DELETE" "/api/v1/companies/{Company_Company Api}"
    Then the response status code should be 403
