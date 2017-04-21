@vm-commands
Feature: Virtual machine states mass actions

  Scenario: Start all virtual machines flagged to be auto-started
    When I run the "vms:dev:update" command with options:
      | action | start |
    Then the command output should be:
      """
      start following instances:
       - Test 1
      """
#    And the "vm-i-00000001-started" mail should not have been sent

  Scenario: Start all virtual machines flagged to be auto-started and send notifications
    When I run the "vms:dev:update" command with options:
      | action   | start |
      | --notify | true  |
    Then the command output should be:
      """
      start following instances:
       - Test 1
      """
    And the "vm-i-00000001-started" mail should have been sent
    And the mail recipients should include:
      | projet-test1@test.com |
    And the mail body should be:
      """
      Hi all !

      The project development platform "Test 1" has been started.

      Its public IP (used for ssh connection) is now : 192.168.1.1

      Full instance information are available from http://{{host.dashboard}}.

      Regards,
      """

  Scenario: Stop all virtual machines flagged to be auto-started
    When I run the "vms:dev:update" command with options:
      | action | stop |
    Then the command output should be:
      """
      stop following instances:
       - Test 2
      """
#    And the "vm-i-00000002-stopped" mail should not have been sent

  Scenario: Stop all virtual machines flagged to be auto-started
    When I run the "vms:dev:update" command with options:
      | action   | stop |
      | --notify | true |
    Then the command output should be:
      """
      stop following instances:
       - Test 2
      """
    And the "vm-i-00000002-stopped" mail should have been sent
    And the mail recipients should include:
      | projet-test2@test.com |
    And the mail body should be:
      """
      Hi all !

      The project development platform "Test 2" has been stopped.

      In case you need to start it, you can do so from http://{{host.dashboard}}.

      Regards,
      """
