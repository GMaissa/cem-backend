@vm-bookmarks
Feature: Virtual machines bookmarks
  As an authenticated user
  I should be able to bookmark and unbookmark virtual machines

  Background: Authenticate
    Given I empty the database
    Then the following fixtures files are loaded:
      | oauth.yml     |
      | users.yml     |
      | bookmarks.yml |
    And I authenticated on OAuth server as "test" and "test"
    And I add "Content-type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"

  Scenario: Retrieve bookmarked virtual machines list
    When I send a "GET" request to "vms/bookmarks"
    Then the response status code should be 200
    And the response should contain "\"id\":\"i-00000001\""
    And the response should not contain "\"id\":\"i-00000002\""

  Scenario: Bookmark a virtual machine
    When I send a "POST" request to "vms/bookmarks" with body:
      """
      {"vmId":"i-00000002"}
      """
    Then the response status code should be 201
    And the response should be empty
    When I send a "GET" request to "vms/bookmarks"
    Then the response status code should be 200
    And the response should contain "\"id\":\"i-00000001\""
    And the response should contain "\"id\":\"i-00000002\""

  Scenario: Bookmark an already bookmarked virtual machine
    When I send a "POST" request to "/vms/bookmarks" with body:
      """
      {"vmId":"i-00000001"}
      """
    Then the response status code should be 409
    And the response should contain "The virtual machine i-00000001 is already bookmarked"

  Scenario: Bookmark a non-existing virtual machine
    When I send a "POST" request to "vms/bookmarks" with body:
      """
      {"vmId":"i-00000003"}
      """
    Then the response status code should be 404
    And the response should contain "No virtual machine found with Id: i-00000003"

  Scenario: Unbookmark a virtual machine
    When I send a "DELETE" request to "vms/bookmarks/i-00000001"
    Then the response status code should be 204
    And the response should be empty
    When I send a "GET" request to "vms/bookmarks"
    Then the response status code should be 204
    And the response should be empty

  Scenario: Unbookmark a non bookmarked virtual machine
    When I send a "DELETE" request to "vms/bookmarks/i-00000002"
    Then the response status code should be 404
    And the response should contain "No user bookmark found for provided virtual machine ID i-00000002"
