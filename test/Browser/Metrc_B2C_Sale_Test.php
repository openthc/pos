<?php
/**
 * Test License Type Behavior
 * We should be able to switch context into a license and get the behavior we expect.
 */

namespace OpenTHC\POS\Test\Browser;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverSelect;
use Facebook\WebDriver\WebDriverExpectedCondition;

class Metrc_B2C_Sale_Test extends \OpenTHC\POS\Test\BaseBrowser
{
	public static function setUpBeforeClass() : void
	{
		parent::setUpBeforeClass();
		self::helperSignIn(getenv('OPENTHC_TEST_COMPANY_A'));
		self::helperSetLicense(getenv('OPENTHC_TEST_LICENSE_A'));

	}

	function test_b2c_sale_basic()
	{
		// #menu-pos
		$this->findElement(WebDriverBy::cssSelector('#menu-pos'))->click();


		$this->assertTextNotExists('Inventory Lots need to be present and priced for the POS to operate [CPH-020]');

		// Click data-contact-id="010DEM0XXXC0NTACT0TEST0000"
		$this->findElement(WebDriverBy::cssSelector(sprintf('[data-contact-id="%s"]', getenv('OPENTHC_TEST_CONTACT_ID'))))->click();

		// Pin Pad
		$this->findElement(WebDriverBy::cssSelector('button[value="5"]'))->click();
		$this->findElement(WebDriverBy::cssSelector('button[value="5"]'))->click();
		$this->findElement(WebDriverBy::cssSelector('button[value="5"]'))->click();
		$this->findElement(WebDriverBy::cssSelector('button[value="5"]'))->click();
		$this->findElement(WebDriverBy::cssSelector('button[value="5"]'))->click();
		$this->findElement(WebDriverBy::cssSelector('button[value="5"]'))->click();
		$this->findElement(WebDriverBy::cssSelector('#btn-auth-next'))->click();

		$this->findElement(WebDriverBy::cssSelector('[value="client-contact-skip"]'))->click();

		$this->findElement(WebDriverBy::cssSelector('#pos-inventory-search'))->click();

		// Wait until .inv-item is visible
		self::$wd->wait()->until(
			WebDriverExpectedCondition::visibilityOfElementLocated(
				WebDriverBy::cssSelector('.inv-item')
			)
		);

		// .inv-item first element
		$el = $this->findElements(WebDriverBy::cssSelector('.inv-item'));
		$e = $el[0];
		$inventory_id = $e->getAttribute('data-id');
		$unit_price0 = $e->getAttribute('data-price');
		$e->click();

		// Wait until `.cart-item[data-id="$inventory_id"]` is visible
		self::$wd->wait()->until(
			WebDriverExpectedCondition::visibilityOfElementLocated(
				WebDriverBy::cssSelector(sprintf('.cart-item[data-id="%s"]', $inventory_id))
			)
		);

		// name="item-01KCPQ8VECC2YYC5H2K2C87EQY-unit-price"
		$e = $this->findElement(WebDriverBy::cssSelector(sprintf('[name="item-%s-unit-price"]', $inventory_id)));
		$unit_price = $e->getText();
		$this->assertEquals($unit_price0, $unit_price);

		// id="pos-shop-next"
		$this->findElement(WebDriverBy::cssSelector('#pos-shop-next'))->click();

		// data-amount="100"
		$e = $this->findElement(WebDriverBy::cssSelector('button[data-amount="100"]'));
		$e->click();

		// $this->assertTextExists('Perfect!');

		// value="pos-done"
		$this->findElement(WebDriverBy::cssSelector('button[value="pos-done"]'))->click();

		$this->assertTextExists('Sale Confirmed, Transaction #');

		// .pos-checkout-reopen
		$this->findElement(WebDriverBy::cssSelector('.pos-checkout-reopen'))->click();

		self::$wd->wait()->until(
			WebDriverExpectedCondition::urlContains('/pos')
		);
	}
}
