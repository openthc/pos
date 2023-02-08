<?php
/**
 * CRM Module
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\POS\Module;

class CRM extends \OpenTHC\Module\Base
{
	function __invoke($a)
	{
		$a->get('', 'OpenTHC\POS\Controller\CRM\Main');
		$a->get('/contact', 'OpenTHC\POS\Controller\CRM\Contact');
		$a->post('/contact', 'OpenTHC\POS\Controller\CRM\Contact:save');
		$a->get('/message', 'OpenTHC\POS\Controller\CRM\Message');
		$a->get('/message/sms', 'OpenTHC\POS\Controller\CRM\Message:sms');
		$a->get('/message/email', 'OpenTHC\POS\Controller\CRM\Message:email');
		$a->get('/ajax', 'OpenTHC\POS\Controller\CRM\Ajax');
	}

}
