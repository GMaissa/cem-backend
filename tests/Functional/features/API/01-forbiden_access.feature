@security
Feature: Authentication
  In order to access protected resource
  As an API client
  I need to be authenticated

  Background:
    Given I empty the database
    Then the fixtures file "oauth.yml" is loaded

  Scenario: Accessing the virtual machines list without being authenticated
    When I send a "GET" request to "vms"
    Then the response status code should be 401
    And the response should be in JSON
    And the response should contain "access_denied"
    And the response should contain "OAuth2 authentication required"

  Scenario: Update a virtual machine state without being authenticated
    When I send a "PUT" request to "vms/i-85740111/start"
    Then the response status code should be 401
    And the response should be in JSON
    And the response should contain "access_denied"
    And the response should contain "OAuth2 authentication required"

  Scenario: Access the user's bookmarked virtual machines without being authenticated
    When I send a "GET" request to "vms/bookmarks"
    Then the response status code should be 401
    And the response should be in JSON
    And the response should contain "access_denied"
    And the response should contain "OAuth2 authentication required"

  Scenario: Bookmark a virtual machine without being authenticated
    When I send a "POST" request to "vms/bookmarks" with body:
      """
      {"vmId": "i-00000001"}
      """
    Then the response status code should be 401
    And the response should be in JSON
    And the response should contain "access_denied"
    And the response should contain "OAuth2 authentication required"

  Scenario: Accessing the virtual machines list with wrong credentials
    Given I authenticated on OAuth server as "test" and "test2"
    When I send a "GET" request to "vms"
    Then the response status code should be 401
    And the response should be in JSON
    And the response should contain "access_denied"
    And the response should contain "OAuth2 authentication required"

  Scenario: Accessing the virtual machines list authenticated with unauthorized domain
    Given I authenticated on OAuth server as "test-unauthorized" and "test"
    When I send a "GET" request to "vms"
    Then the response status code should be 401
    And the response should be in JSON
    And the response should contain "access_denied"
    And the response should contain "OAuth2 authentication required"
