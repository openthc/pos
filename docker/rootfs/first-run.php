<?php
/**
 * Install Tool
 */

require_once('/opt/openthc/pos/boot.php');


echo "INSTALL FIRST RUN POS\n";

sleep(3);

exit(0);

header('cache-control: no-store, max-age=0');

$file = '/opt/openthc/gfs/app-config.php';
if (is_file($file)) {
	$html = <<<HTML
<h1>This system is already configured!</h1>
<h2>Edit this config file in the container</h2>
<pre>$file</pre>
<p>Restart the container when finished</p>
HTML;
	_exit_html_warn($html);
}


switch ($_POST['a']) {

	case 'config-update':

		$file = '/opt/openthc/gfs/pos-config.php';
		$data = $_POST['service-config'];
		$data = json_decode($data, true);
		$data = var_export($data, true);
		$data = "<?php\n// Generated file\n\nreturn $data;\n";
		file_put_contents($file, $data);

		$html = <<<HTML
<h1>This system is configured!</h1>
<h2>Please restart this container</h2>
HTML;
		_exit_html_warn($html);

		break;

}

$cfg0 = require_once('/opt/openthc/pos/etc/config-example.php');
$code = json_encode($cfg0, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

?>

<div style="max-width: 1120px;">
<form method="post">

<h1>Configure OpenTHC POS</h1>

<textarea name="service-config" style="width:100%; height: 40rem;"><?= htmlspecialchars($code); ?></textarea>

<button name="a" type="submit" value="config-update">Save</button>

<script>
var host = window.location.hostname;
var port = window.location.port;

var textNode = window.document.querySelector('textarea');

if ((port >= 4200) && (port <= 4299)) {
	// Try to detect my other services on other ports?
	text = textNode.value.replace(/example\.com/g, `${host}:${port}`);
	// textNode.value = text;
}

</script>

</form>
</div>
