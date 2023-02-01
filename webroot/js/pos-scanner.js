/**
 * POS Scanner
 * Tries to be a Helper for reading PDF417 from Bluetooth or USB Scanner
 */


var POS = POS || {};

POS.Scanner = {};
POS.Scanner.callback = undefined;
POS.Scanner.data = [];

POS.Scanner.done = function()
{
	var data = POS.Scanner.data.join(' ');
	data = data.replace(/Shift/g, ' ');
	data = data.replace(/Control J/g, '<br>--Control J--<br>');
	data = data.replace(/\[CR\]$/g, '');
	$('#scan-input-data').html(data);

	if (POS.Scanner.callback) {
		POS.Scanner.callback(POS.Scanner.data);
		POS.Scanner.callback = undefined;
	}
}


POS.Scanner.live = function(node, callback)
{
	POS.Scanner.callback = callback;
	POS.Scanner.data = [];
	$(document.body).on('keydown', POS.Scanner.read);
	$(node).html('Ready to Scan');

}

/**
 * Read A Character
 * @param {[type]} e [description]
 * @return {[type]} [description]
 */
POS.Scanner.read = function(e)
{
	//console.log(e);

	var c = String.fromCharCode(e.which);

	console.log({
		c: c,
		which: e.which,
		key: e.key,
		keyCode: e.keyCode,
		char: e.char,
		charCode: e.charCode,
		alt: e.altKey,
		ctrl: e.ctrlKey,
		meta: e.metaKey,
		shift: e.shiftKey,
	});

	switch (e.which) {
	case 13: // Enter
		POS.Scanner.stop();
		POS.Scanner.done();
		break;
	case 16: // Shift
	case 17: // Ctrl
	case 18: // Alt
		c = e.key;
		break;
	case 91: // Windows Key
	case 93: // Windows Context Key
		return false;
	case 186:
		if (e.shiftKey) {
			c = ':';
		} else {
			c = ';';
		}
		break;
	case 190:
		c = '.';
		if (e.shiftKey) {
			c = '>';
		}
		break;
	case 191: // /
		c = '/';
		if (e.shiftKey) {
			c = '?';
		}
		break;
	case 192:
		if (e.shiftKey) {
			c = '~';
		} else {
			c = '`';
		}
		break;
	}

	//POS.Scanner.data.push(e.keyCode);
	POS.Scanner.data.push(c);
	return false;

}

POS.Scanner.stop = function()
{
	$(document.body).off('keydown', POS.Scanner.read);
}
