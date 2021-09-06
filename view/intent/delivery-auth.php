<?php
/**
 * Form to Authenticate a Delivery Person
 */

$this->data['Page']['title'] = 'Delivery Staff Authentication';

if (empty($_COOKIE['pos-contact'])) {
	// Prompt for Username
}

require_once(sprintf('%s/view/pos/open.php', APP_ROOT));
