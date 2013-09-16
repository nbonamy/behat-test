Feature: ls

	Scenario: List 2 files in a directory
		Given I am in directory "test"
		And I have a file named "foo"
		And I have a file named "bar"
		When I run "ls"
		Then I should get:
			"""
			bar
			foo
			"""
