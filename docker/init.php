#!/usr/bin/env php
<?php
/**
 * OpenTHC Docker POS Application Init
 */

_init_config();

// Bootstrap OpenTHC Service
$d = dirname(__DIR__);
require_once("$d/boot.php");
// require_once("$d/vendor/openthc/common/lib/docker.php");

// Wait for Database
$dsn = getenv('OPENTHC_DSN_MAIN');
$dbc_main = _spin_wait_for_sql($dsn);

$dsn = getenv('OPENTHC_DSN_AUTH');
$dbc_auth = _spin_wait_for_sql($dsn);

_upsert_this_service($dbc_main, $dbc_auth);

exit(0);

/**
 * Create Service Config File
 */
function _init_config()
{
	$cfg = [];

	$cfg['database'] = [
		'auth' => [
			'dsn' => getenv('OPENTHC_DSN_AUTH'),
			'hostname' => 'sql',
			'username' => 'openthc_auth',
			'password' => 'openthc_auth',
			'database' => 'openthc_auth',
		],
		'main' => [
			'dsn' => getenv('OPENTHC_DSN_MAIN'),
			'hostname' => 'sql',
			'username' => 'openthc_main',
			'password' => 'openthc_main',
			'database' => 'openthc_main',
		],
	];

	// Redis
	$cfg['redis'] = [
		'hostname' => 'rdb',
	];

	// OpenTHC Services
	$cfg['openthc'] = [
		'app' => [
			'origin' => getenv('OPENTHC_APP_ORIGIN'),
			'public' => getenv('OPENTHC_APP_PUBLIC'),
		],
		'b2b' => [
			'origin' => getenv('OPENTHC_B2B_ORIGIN'),
			'public' => getenv('OPENTHC_B2B_PUBLIC'),
		],
		'cre' => [
			'origin' => getenv('OPENTHC_CRE_ORIGIN'),
			'public' => getenv('OPENTHC_CRE_PUBLIC'),
		],
		'dir' => [
			'origin' => getenv('OPENTHC_DIR_ORIGIN'),
			'public' => getenv('OPENTHC_DIR_PUBLIC'),
		],
		'pos' => [
			'id' => getenv('OPENTHC_POS_ID'),
			'origin' => getenv('OPENTHC_POS_ORIGIN'),
			'public' => getenv('OPENTHC_POS_PUBLIC'),
			'secret' => getenv('OPENTHC_POS_SECRET'),
		],
		'pub' => [
			'origin' => getenv('OPENTHC_PUB_ORIGIN'),
			'public' => getenv('OPENTHC_PUB_PUBLIC'),
		],
		'sso' => [
			'id' => getenv('OPENTHC_SSO_ID'),
			'origin' => getenv('OPENTHC_SSO_ORIGIN'),
			'public' => getenv('OPENTHC_SSO_PUBLIC'),
			'client-id' => getenv('OPENTHC_POS_ID'),
			'client-pk' => getenv('OPENTHC_POS_PUBLIC'),
			'client-sk' => getenv('OPENTHC_POS_SECRET'),
		]
	];

	$cfg_data = var_export($cfg, true);
	$cfg_text = sprintf("<?php\n// Generated File\n\nreturn %s;\n", $cfg_data);
	$cfg_file = sprintf('%s/etc/config.php', dirname(__DIR__));

	file_put_contents($cfg_file, $cfg_text);

}


/**
 *
 */
function _upsert_this_service($dbc_main, $dbc_auth)
{
	$arg = [];
	$arg[':s1'] = getenv('OPENTHC_POS_ID');
	$arg[':c1'] = getenv('OPENTHC_ROOT_COMPANY_ID');
	$arg[':pk1'] = getenv('OPENTHC_POS_PUBLIC');
	$arg[':sk1'] = getenv('OPENTHC_POS_SECRET');

	$sql = <<<SQL
	INSERT INTO public.auth_service (id, company_id, created_at, stat, flag, name, code, hash, context_list)
	VALUES (:s1, :c1, '2014-04-20', 0, 0, 'OpenTHC/Demo/POS', :pk1, :sk1, 'company contact license profile cre pos')
	ON CONFLICT (id) DO UPDATE SET
		company_id = EXCLUDED.company_id
		, code = EXCLUDED.code
		, hash = EXCLUDED.hash
	SQL;
	$dbc_auth->query($sql, $arg);

}


/**
 *
 */
function _spin_wait_for_sql(string $dsn)
{

	$try = 0;

	do {

		$try++;

		try {
			// echo "SQL Connection: Checking\n";

			$ret = new \Edoceo\Radix\DB\SQL($dsn);

			return $ret;

		} catch (Exception $e) {
			// Ignore
			// echo "SQL Connection: ";
			// echo $e->getMessage();
			// echo "\n";
			// var_dump($e);
		}

		sleep(4);

	} while ($try < 16);

	throw new \Exception('Failed to connect to database');

	exit(1);
}
