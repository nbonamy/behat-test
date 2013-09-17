Feature: Angular

	Background:
    Given I am on "http://www.bonamy.fr/angular"
    And I wait 1 second

  Scenario: Check list
    Then I should see 5 "transaction" elements
    And I should see 1 "negative transaction" element

  Scenario: About
  	When I follow "About"
  	And I wait 1 second
  	Then I should see "This is a sample app"

  Scenario: Invalid filter
  	When I fill in "filter" with "Nonsense"
  	Then I should see 0 "transaction" element

  Scenario: Valid filter
  	When I fill in "filter" with "nt"
  	Then I should see 3 "transaction" elements
  	And all "transaction" "description" should contain "nt"
  	And all "transaction" "description" should be
  	"""
  	Internet
  	Internet
  	Remboursement
  	"""

  Scenario Outline: More filters
    When I fill in "filter" with "<value>"
    Then I should see <result> "transaction" element

    Examples:
      | value | result |
      | nt    | 3      |
      | cou   | 2      |
      | zzz   | 0      |
      | s     | 3      |
      |       | 5      |
