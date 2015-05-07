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
    When I make request "GET" "/api/v1/companies/{Company:Company Api}"
    Then the response status code should be 200
    And the response should be JSON
    And the response JSON should be a single object
    And the repsonse JSON should have "id" field
    And the repsonse JSON should have "name" field with value "Company Api"
    And the repsonse JSON should have "website_url" field with value "http://company.api"
    And the repsonse JSON should have "logo_url" field with value "http://company.api/logo.png"
