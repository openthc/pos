<?php
/**
 * Decrypt the Token or Throw
 */

namespace OpenTHC\POS\Traits;

trait OpenAuthBox
{
	function open_auth_box(string $cpk, string $box) : array
	{
		$box = \OpenTHC\Sodium::b64decode($box);

		$ssk = \OpenTHC\Config::get('openthc/pos/secret');
		$act = \OpenTHC\Sodium::decrypt($box, $ssk, $cpk);
		if (empty($act)) {
			throw new \Exception('Authentication Box Invalid Service Key [PCB-025]', 403);
		}
		$act = json_decode($act);
		if (empty($act)) {
			throw new \Exception('Authentication Box Invalid Service Key [PCB-029]', 403);
		}
		if (sodium_compare($act->pk, $cpk) !== 0) {
			throw new \Exception('Authentication Box Invalid Service Key [PCB-032]', 403);
		}

		// Time Check
		$dt0 = new \DateTime();
		$dt1 = \DateTime::createFromFormat('U', $act->ts);
		$age = $dt0->diff($dt1, true);
		if (($age->d != 0) || ($age->h != 0) || ($age->i > 5)) {
			throw new \Exception('Authentication Box Expired [PCB-040]', 400);
		}

		if (empty($act->contact)) {
			throw new \Exception('Authentication Box Data Corrupted [PCB-103]', 403);
		}

		if (empty($act->company)) {
			throw new \Exception('Authentication Box Data Corrupted [PCB-110]', 403);
		}

		return $act;
	}

}
