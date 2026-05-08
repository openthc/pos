<?php
/**
 * Sign-in to the POS application directly
 */

namespace OpenTHC\POS\Test\Auth;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

class UI_Auth_Test extends \OpenTHC\POS\Test\BaseBrowser
{

	function test_sign_in()
	{
		// Nav to TEST_ORIGIN /auth/open
		self::$wd->get(getenv('OPENTHC_TEST_ORIGIN') . '/auth/open');

		// Wait until we see `id="username"`
		self::$wd->wait()->until(
			WebDriverExpectedCondition::presenceOfElementLocated(
				WebDriverBy::cssSelector('#username')
			)
		);
		// Fill in username and password
		$e = $this->findElement(WebDriverBy::name('username'));
		$e->clear();
		$text = getenv('OPENTHC_TEST_CONTACT_USERNAME');
		$e->sendKeys($text);
		$e = $this->findElement(WebDriverBy::name('password'));
		$e->clear();
		$text = getenv('OPENTHC_TEST_CONTACT_PASSWORD');
		$e->sendKeys($text);

		// Click the sign-in button
		$e = $this->findElement(WebDriverBy::cssSelector('button#btn-auth-open'));
		$e->click();

		// Wait until we see xpath "//h1[contains(text(), 'Select Company')]"
		self::$wd->wait()->until(
			WebDriverExpectedCondition::presenceOfElementLocated(
				WebDriverBy::xpath("//h1[contains(text(), 'Select Company')]")
			)
		);
		$company_id = getenv('OPENTHC_TEST_COMPANY_A');
		// Wait until we see the company button with id `btn-company-{COMPANY_A}`
		self::$wd->wait()->until(
			WebDriverExpectedCondition::presenceOfElementLocated(
				WebDriverBy::cssSelector(sprintf('#btn-company-%s', $company_id))
			)
		);
		// Click the company button with id `btn-company-{COMPANY_A}`
		$e = $this->findElement(
			WebDriverBy::cssSelector(sprintf('#btn-company-%s', $company_id))
		);
		$e->click();

		// Wait until we see xpath "//h1[contains(text(), 'Pick License')]"
		self::$wd->wait()->until(
			WebDriverExpectedCondition::presenceOfElementLocated(
				WebDriverBy::xpath("//h1[contains(text(), 'Pick License')]")
			)
		);
		// Click the license button with id `data-license-id="{LICENSE_A}"`
		$license_id = getenv('OPENTHC_TEST_LICENSE_A');
		$e = $this->findElement(
			WebDriverBy::cssSelector(sprintf('[data-license-id="%s"]', $license_id))
		);
		$e->click();

		// Wait until we see xpath "//*[contains(text(), 'Open Register')]"
		self::$wd->wait()->until(
			WebDriverExpectedCondition::presenceOfElementLocated(
				WebDriverBy::xpath("//*[contains(text(), 'Open Register')]")
			)
		);
		$this->assertTrue(true);
	}

}