#
# This describes the behavior of the POS application and 
# the basic features of the point-of-sale interface 
#

Feature: Point of Sale

	In order to release product from the custody of a retail licensee
	Retail Contacts should be able to sell products to customers

	Scenario: Open the POS application from the App Dashboard
	Given I am a Retail Contact
	When I click Launch POS
	Then I should see the POS Dashboard

	Scenario: Open the POS application's Point-Of-Sale interface
	Given I am a Retail Contact logged into POS
	When I click the POS icon
	And I enter "1234" as my Employee Code
	Then I should see the point-of-sale interface

	Scenario: Find all active Products
	Given I am a Retail Contact with the POS interface open
	When I click the Search icon
	Then all active Products display in the list categorized by Product Type

	Scenario: Add a Product to the active Ticket
	Given I am a Retail Contact with the POS interface open
	When I click on a Product
	Then it is added to the Product list in the active Ticket

	Scenario: Add the same Product to the active Ticket
	Given I am a Retail Contact with one Product in the active Ticket
	When I click on the same Product in the active Products list
	Then the quantity of that Product in the active Ticket is increased by 1

	Scenario: Open Payment interface for an active Ticket
	Given I am a Retail Contact with an active Ticket worth $10.95
	When I press the Payment button
	Then I can see the payment interface

	Scenario: Process payment for active Ticket
	Given I am a Retail Contact with an active Ticket worth $10.95
	When I press $20 in the payment interface
	Then I see the $9.05 change due

	Scenario: Complete the sale for the active Ticket
	Given I am a Retail Contact that has processed all change due
	When I press the Complete button
	Then I see the Recept interface
