<?php
/**
 * https://php-webdriver.github.io/php-webdriver/1.4.0/Facebook/WebDriver/Remote/RemoteWebDriver.html
 */

namespace OpenTHC\POS\Test;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;

class BaseBrowser extends \OpenTHC\Test\BaseBrowser
{
	protected $_app_base;

	protected function setUp() : void
	{
		parent::setUp();
		$this->_app_base = getenv('OPENTHC_TEST_ORIGIN');
	}

	/**
	 * Set up before class PHPUnit hook.
	 * Do not use helperSignIn, helperSetLicense here, because this removes the flexibility of per-test sign-in.
	 */
	public static function setUpBeforeClass() : void
	{
		$_ENV['OPENTHC_TEST_WEBDRIVER_URL'] = getenv('OPENTHC_TEST_WEBDRIVER_URL');
		parent::setUpBeforeClass();
	}

	public static function helperSignIn($company_id)
	{
		self::$wd->get(getenv('OPENTHC_TEST_ORIGIN') . '/auth/open');
		$e = self::$wd->findElement(WebDriverBy::name('username'));
		$e->clear();
		$text = getenv('OPENTHC_TEST_CONTACT_USERNAME');
		$e->sendKeys($text);

		$e = self::$wd->findElement(WebDriverBy::name('password'));
		$e->clear();
		$text = getenv('OPENTHC_TEST_CONTACT_PASSWORD');
		$e->sendKeys($text);

		$e = self::$wd->findElement(WebDriverBy::cssSelector('button#btn-auth-open'));
		$e->click();

		// Identify a Company by ID
		$e = self::$wd->findElement(WebDriverBy::cssSelector(sprintf('#btn-company-%s', $company_id)));
		// scroll into view
		self::$wd->executeScript('arguments[0].scrollIntoView({ behavior: "instant", block: "center", inline: "center" });', [ $e ]);
		$e->click();
	}

	public static function helperSetLicense($license_id)
	{
		// Identify a License by ID
		$e = self::$wd->findElement(WebDriverBy::cssSelector(sprintf('[data-license-id="%s"]', $license_id)));
		self::$wd->executeScript('arguments[0].scrollIntoView({ behavior: "instant", block: "center", inline: "center" });', [ $e ]);
		$e->click();
	}


	function assertTextExists(string $text)
	{
		$by = WebDriverBy::xpath(sprintf('//*[contains(text(), "%s")]', $text));
		$elements = $this->findElements($by);
		$this->assertNotEmpty($elements, 'Failed asserting that text exists: ' . $text);
	}

	function assertTextNotExists(string $text)
	{
		$by = WebDriverBy::xpath(sprintf('//*[contains(text(), "%s")]', $text));
		$elements = $this->findElements($by);
		$this->assertEmpty($elements, 'Failed asserting that text does not exist: ' . $text);
	}
}
