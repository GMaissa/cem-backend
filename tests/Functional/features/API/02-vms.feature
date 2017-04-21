@vm
Feature: Virtual machines

  Background:
    Given I empty the database
    Then the following fixtures files are loaded:
      | oauth.yml     |
      | users.yml     |
    And I authenticated on OAuth server as "test" and "test"
    And I add "Content-type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"

  Scenario: Retrieve all development virtual machines data
    When I send a GET request to vms
    Then the response status code should be 200
    And the response should be in JSON
    And the response should contain "\"id\":\"i-00000001\""
    And the response should not contain "\"id\":\"i-00000002\""

  Scenario: Update a virtual machine state being authenticated
    When I send a "PUT" request to "vms/i-00000001/start"
    Then the response status code should be 200
    And the response should be in JSON
    And the "vm-i-00000001-started" mail should have been sent
    And the mail recipients should include:
      | projet-test1@test.com |
    And the mail body should be:
      """
      Hi all !

      The project development platform "Test 1" has been started by Te St.

      Its public IP (used for ssh connection) is now : 192.168.1.1

      Full instance information are available from http://{{host.dashboard}}.

      Regards,
      """

  Scenario: Update a virtual machine state being authenticated
    When I send a "PUT" request to "vms/i-00000002/stop"
    Then the response status code should be 200
    And the response should be in JSON
    And the "vm-i-00000002-stopped" mail should have been sent
    And the mail recipients should include:
      | projet-test2@test.com |
    And the mail body should be:
      """
      Hi all !

      The project development platform "Test 2" has been stopped by Te St.

      In case you need to start it, you can do so from http://{{host.dashboard}}.

      Regards,
      """
