<?php
/**
 * https://developer.weedmaps.com/
 */

namespace App\Service;

class Weedmaps
{
	protected $_auth_token;
	protected $_public;
	protected $_secret;

	function __construct($cfg)
	{
		$this->_public = $cfg['public'];
		$this->_secret = $cfg['secret'];
	}

	function getAuthToken()
	{
		$req = _curl_init('https://api-g.weedmaps.com/auth/token');

		$req_body = json_encode([
			'client_id' => $this->_public,
			'client_secret' => $this->_secret,
			'grant_type' => 'client_credentials',
			'scope' => 'taxonomy:read brands:read products:read menu_items menus:write',
		]);
		curl_setopt($req, CURLOPT_POST, true);
		curl_setopt($req, CURLOPT_POSTFIELDS, $req_body);

		$req_head = [];
		$req_head[] = 'accept: application/json';
		$req_head[] = 'content-type: application/json';
		// $req_head[] =

		curl_setopt($req, CURLOPT_HTTPHEADER, $req_head);

		$res = curl_exec($req);
		$res = json_decode($res, true);

		if (!empty($res['created_at']) && !empty($res['expires_in'])) {
			$res['expires_at'] = $res['created_at'] + $res['expires_in'];
			$res['expires_at_iso'] = date(\DateTime::ISO8601, $res['expires_at']);
		}

		$this->setAuthToken($res['access_token']);

		return $res;

	}

	function setAuthToken($t)
	{
		$this->_auth_token = $t;
	}

	/**
	 *
	 */
	function get($path)
	{
		$url = sprintf('https://api-g.weedmaps.com%s', $path);

		$req = _curl_init($url);

		$req_head = [];
		$req_head[] = 'accept: application/json';
		$req_head[] = sprintf('authorization: Bearer %s', $this->_auth_token);
		curl_setopt($req, CURLOPT_HTTPHEADER, $req_head);

		$res = curl_exec($req);
		$res = json_decode($res, true);

		return $res;

	}

	/**
	 *
	 */
	function post_form($path, $body)
	{
		$url = sprintf('https://api-g.weedmaps.com%s', $path);
		$req = _curl_init($url);
		curl_setopt($req, CURLOPT_POST, true);
		curl_setopt($req, CURLOPT_POSTFIELDS, $body);

		$req_head = [];
		$req_head[] = 'accept: application/json';
		$req_head[] = sprintf('authorization: Bearer %s', $this->_auth_token);
		$req_head[] = 'content-type: application/x-www-form-urlencoded';
		curl_setopt($req, CURLOPT_HTTPHEADER, $req_head);

		$res = curl_exec($req);
		$res = json_decode($res, true);

		return $res;

	}

	/**
	 *
	 */
	function post_json($path, $body)
	{
		$url = sprintf('https://api-g.weedmaps.com%s', $path);
		$req = _curl_init($url);
		curl_setopt($req, CURLOPT_POST, true);
		curl_setopt($req, CURLOPT_POSTFIELDS, $body);

		$req_head = [];
		$req_head[] = 'accept: application/json';
		$req_head[] = sprintf('authorization: Bearer %s', $this->_auth_token);
		$req_head[] = 'content-type: application/json';
		curl_setopt($req, CURLOPT_HTTPHEADER, $req_head);

		$res = curl_exec($req);
		$res = json_decode($res, true);

		return $res;

	}
}
