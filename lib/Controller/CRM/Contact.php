<?php
/**
 * CRM Home
 */

namespace App\Controller\CRM;

class Contact extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		$data = array(
			'Page' => array('title' => 'CRM :: Contact List'),
		);

		$dbc = $this->_container->DB;
		$sql = 'SELECT * FROM contact WHERE email IS NOT NULL OR phone IS NOT NULL ORDER BY id LIMIT 50';
		$data['contact_list'] = $dbc->fetchAll($sql);

		return $this->_container->view->render($RES, 'page/crm/contact.html', $data);

	}

	function save($REQ, $RES, $ARG)
	{
		// var_dump($_POST);

		$dbc = $this->_container->DB;

		$rec = [
			'id' => _ulid(),
			'fullname' => trim($_POST['contact-name']),
			'email' => trim(strtolower($_POST['contact-email'])),
			'phone' => trim($_POST['contact-phone']),
			'hash' => '-',
			// 'tags' =>
		];
		$rec['guid'] = $rec['id'];

		$dbc->insert('contact', $rec);

		return $RES->withRedirect('/crm/contact');
	}

}
