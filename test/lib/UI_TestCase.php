<?php
/**
	https://gist.github.com/aczietlow/7c4834f79a7afd920d8f
*/

namespace Test;

use Facebook\WebDriver\WebDriverBy;

class UI_TestCase extends \PHPUnit\Framework\TestCase
{
	/**
		Load a Page
	*/
	public function getPage($u)
	{
		$this->_wd->get($u);
		// Check for PHP Errors, get text, or source or HTML and clean-up or something, then Assert? Make a evalPHPErrors common routine?
		//$html = self::$wd->getPageSource();
		//$this->assertNotRegExp('/parse error/im', $html );
		//$this->assertNotRegExp('/syntax error/im', $html );
		//$this->assertNotRegExp('/error:.+in.+on line \d+/im', $html );
		//$this->assertNotRegExp('/notice:.+in.+on line \d+/im', $html );
		//$this->assertNotRegExp('/warning:.+in.+on line \d+/im', $html );
		//return $html;
	}

	/**
		Get an Element by Selector, does magic string promotion
	*/
	public function findElement($find)
	{
		if (is_object($find)) {
			// OK
		} elseif (is_string($find)) {
			if (preg_match('/^([\#\.])(.+)$/', $find, $m)) {
				switch ($m[1]) {
				case '#':
					$find = WebDriverBy::id($m[2]);
					break;
				case '.':
					$find = WebDriverBy::className($m[2]);
					break;
				}
			} elseif (preg_match('/^\/\/.+$/', $find)) {
				$find = WebDriverBy::xpath($find);
			} else {
				$find = WebDriverBy::cssSelector($find);
			}
		}

		$e = $this->_wd->findElement($find);

		return $e;
	}

	/**
		Wrap WebDriver
	*/
	public function findElements($find)
	{
		return $this->_wd->findElements($find);
	}

}
