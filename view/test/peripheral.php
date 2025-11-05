<?php
/**
 * Test Routines for:
 * WebSerial, WebUSB, WebHID and ... ?
 *
 * SPDX-License-Identifier: MIT
 */

?>


<div class="container mt-4" id="test-page-wrap">

<section class="mb-4">
	<h2>Keyboard</h2>
	<p>Activate this Test, Start Scanning your Device and choose Connect</p>
	<button class="btn btn-primary" id="keyboard-connect">Begin Test</button>
	 <pre id="keyboard-output"></pre>
</section>

<section class="mb-4">
	<h2>WebSerial</h2>
	<p>Activate this Test, Select your Device and choose Connect</p>
	<button class="btn btn-primary" id="webserial-connect">Begin Test</button>
	<pre id="webserial-output"></pre>
</section>

<section class="mb-4">
	<h2>WebUSB</h2>
	<p>Activate this Test, Select your Device and choose Connect</p>
	<button class="btn btn-primary" id="webusb-connect">Begin Test</button>
	<pre id="webusb-output"></pre>
</section>


<section class="mb-4">
	<h2>WebHID</h2>
	<p>Activate this Test, Select your Device and choose Connect</p>
	<button class="btn btn-primary" id="webhid-connect">Begin Test</button>
	<pre id="webhid-output"></pre>
</section>

<section class="mb-4">
	<h2>Presentation</h2>
	<p>Activate this Test, Select your Device and choose Connect</p>
	<button class="btn btn-primary" disabled id="presentation-test-init">Begin Test</button>
	<button class="btn btn-secondary" disabled id="presentation-test-send">Send Message</button>
	<button class="btn btn-danger" disabled id="presentation-test-stop">Stop</button>
	<pre id="presentation-output"></pre>
</section>

</div>

<script>
	// keyboard-connect
</script>

<script>
async function connectWebSerial() {

	if (!('serial' in navigator)) {
		return;
	}

	try {
		// Open device picker
		const port = await navigator.serial.requestPort(); // user picks device

		// Configure port: common barcode scanners use 9600, 8N1 but check your scanner manual
		await port.open({ baudRate: 9600, dataBits: 8, stopBits: 1, parity: 'none', flowControl: 'none' });

		$('#webserial-output').append('Port opened — starting read loop');

		// Set up a reader that yields text lines
		const textDecoder = new TextDecoderStream();
		const readableStreamClosed = port.readable.pipeTo(textDecoder.writable);
		const reader = textDecoder.readable
			.pipeThrough(new TransformStream(new LineBreakTransformer())) // helper below
			.getReader();

		while (true) {
			const { value, done } = await reader.read();
			if (done) break;
			// value is a full line (barcode)
			$('#webserial-output').append(`Scanned: ${value}`);
			// do something with `value` (send to server, look up DB, etc.)
		}

	} catch (err) {
		$('#webserial-output').append(`<span class="text-danger">WebUSB Error: ${err}</span>`);
	}
}

// helper to split stream into lines
class LineBreakTransformer {
	constructor() { this.container = ''; }
	transform(chunk, controller) {
		this.container += chunk;
		const lines = this.container.split(/\r\n|[\r\n]/);
		this.container = lines.pop();
		for (const line of lines) controller.enqueue(line);
	}
	flush(controller) {
		if (this.container) controller.enqueue(this.container);
	}
}
</script>

<!-- WebUSB Code -->
<script>
async function connectWebUSB() {

	if (!('usb' in navigator)) {
		return;
	}

	// Filter helps the device picker show relevant devices. Replace vendorId/productId if you know them.
	const filters = [
		// { vendorId: 0x05e0, productId: 0x1200 }, // example vendor/product (replace)
		{ vendorId: 0x05e0, productId: 0x1200 }, //  Symbol Technologies Bar Code Scanner
	];

	try {

		const device = await navigator.usb.requestDevice({ filters });

		await device.open();
		if (device.configuration === null) {
			await device.selectConfiguration(1);
		}
		// Choose interfaceIndex that has the endpoints you need. Many scanners have interface 0 or 1.
		const interfaceNumber = device.configuration.interfaces[0].interfaceNumber;
		await device.claimInterface(interfaceNumber);

		// Find IN endpoint (device->host) for bulk transfers
		const iface = device.configuration.interfaces.find(i => i.interfaceNumber === interfaceNumber);
		const endpointIn = iface.alternate.endpoints.find(e => e.direction === 'in').endpointNumber;

		console.log('Starting transfer loop on endpoint', endpointIn);

		while (device.opened) {
		// transferIn returns a USBInTransferResult
		const result = await device.transferIn(endpointIn, 64); // 64-byte chunk; adjust as needed
		if (result.status === 'ok' && result.data) {
			// result.data is a DataView
			const chunk = new TextDecoder().decode(result.data);
			console.log('Chunk:', chunk);
			// scanners often send entire barcode as ASCII terminated by \r or \n
			// buffer and split on newlines to assemble complete codes
		} else {
			console.warn('Transfer status', result.status);
			break;
		}
		}

	} catch (err) {
		$('#webusb-output').append(`<span class="text-danger">WebUSB Error: ${err}</span>`);
	}
}
</script>


<!-- WebHID Code -->
<script>
async function connectWebHID() {

	if (!('hid' in navigator)) {
		return;
	}

	try {
		const devices = await navigator.hid.requestDevice({ filters: [] });
		if (devices.length === 0) return;
		const device = devices[0];
		await device.open();
		console.log('Connected to', device.productName);

		device.addEventListener('inputreport', event => {
			const { data, reportId } = event;
			// Raw HID data is in data (DataView)
			console.log('Report', reportId, new Uint8Array(data.buffer));
		});
	} catch (err) {
		$('#webhid-output').append(`<span class="text-danger">WebHID Error: ${err}</span>`);
	}
};
</script>

<!-- Keyboard Handler -->
<script>
if (!('keyboard' in navigator)) {
	$('#keyboard-connect').removeClass('btn-primary');
	$('#keyboard-connect').addClass('btn-danger');
	$('#keyboard-connect').attr('disabled', true);
	$('#keyboard-output').append('<span class="text-danger">Keyboard API is not supported in this browser</span>');
} else {
	var kbd_hook = false;
	var kbd_result = [];
	var kbd_callback = function(e) {

		console.log('keydown', e);

		e.preventDefault();

		var key = e.key;
		switch (e.key) {
			case 'Control':
				key = 'CTRL';
				break;
			case 'Escape':
				key = 'ESC';
				break;
			case 'Shift':
				key = 'SHIFT';
				break;
		}

		kbd_result.push(key);

		return false;
	};
	document.getElementById('keyboard-connect').addEventListener('click', function(e) {
		if (!kbd_hook) {
			kbd_hook = true;
			window.addEventListener('keydown', kbd_callback);
			$('#keyboard-output').empty();
			$('#keyboard-output').append("Keyboard listener attached\n");
		} else {
			window.removeEventListener('keydown', kbd_callback);
			$('#keyboard-output').append(kbd_result.join('') + "\n");
			$('#keyboard-output').append("Keyboard listener detached\n");
			kbd_result = [];
			kbd_hook = false;
		}
	});
	// $('#keyboard-output').append('Keyboard Listener Attached');
	// const kbd_result = navigator.keyboard.lock();
	// kbd_result.then(function(a) {
	// 	console.log('kbd_result', a);
	// });
	// console.log(kbd_result);

	// document.getElementById('keyboard-connect').addEventListener('click', function(e) {
	// 	console.log('unlock');
	// 	navigator.keyboard.unlock();
	// });

}
if (!('hid' in navigator)) {
	$('#webhid-connect').removeClass('btn-primary');
	$('#webhid-connect').addClass('btn-danger');
	$('#webhid-connect').attr('disabled', true);
	$('#webhid-output').append('<span class="text-danger">WebHID is not supported in this browser</span>');
} else {
	document.getElementById('webhid-connect').addEventListener('click', connectWebHID);
}
if (!('serial' in navigator)) {
	$('#webserial-connect').removeClass('btn-primary');
	$('#webserial-connect').addClass('btn-danger');
	$('#webserial-connect').attr('disabled', true);
	$('#webserial-output').append('<span class="text-danger">WebSerial is not supported in this browser</span>');
} else {
	document.getElementById('webserial-connect').addEventListener('click', connectWebSerial);
}
if (!('usb' in navigator)) {
	$('#webusb-connect').removeClass('btn-primary');
	$('#webusb-connect').addClass('btn-danger');
	$('#webusb-connect').attr('disabled', true);
	$('#webusb-output').append('<span class="text-danger">WebUSB is not supported in this browser</span>');
} else {
	document.getElementById('webusb-connect').addEventListener('click', connectWebUSB);
}
</script>

<!-- Presentation Integration -->
<script>
function presentation_test()
{
	if (!('presentation' in navigator)) {
		return;
	}

	$('#presentation-test-init').attr('disabled', false);
	$('#presentation-test-send').attr('disabled', false);
	$('#presentation-test-stop').attr('disabled', false);

	if ('#presentation-test-client' == window.location.hash) {

		$('#test-page-wrap').empty();
		$('#test-page-wrap').append('<h1>Presentation Test Client</h1>');
		$('#test-page-wrap').append('<pre id="presetnation-test-client-output"></pre>');

		navigator.presentation.receiver.connectionList.then(list => {
			list.connections.forEach(connection => {
				connection.addEventListener('message', e => {
					console.log('Presentation Test Client RX', e);
					// document.getElementById('msg').textContent = e.data;
					$('#presetnation-test-client-output').append(e.data);
				});
			});
		});

		return;

	}

	var ext_conn = null;
	document.getElementById('presentation-test-init').addEventListener('click', async function() {
		try {

			const request = new PresentationRequest('https://pos.openthc.dev/test/peripheral#presentation-test-client');
			// Optionally listen for connection events
			request.addEventListener('connectionavailable', e => {
				// const connection = e.connection;
				console.log('Connected to presentation!');
				// connection.send('Hello from controller!');
			});
			// Let the user choose a display
			ext_conn = await request.start();

			console.log("Connected to presentation:", ext_conn.id);

			ext_conn.onmessage = event => {
				console.log("Receiver says:", event.data);
			};

		} catch (err) {
			console.error("Failed to start presentation:", err);
		}
	});
	document.getElementById("presentation-test-send").onclick = () => {
		console.log('Presentation Sending');
		if (ext_conn) {
			ext_conn.send("Hello from the controller!\n");
		}
	}

	$('#presentation-test-stop').on('click', function() {
		ext_conn.terminate();
		ext_conn.close()
		ext_conn = null;
	});
}
presentation_test();
</script>

<script>
async function listAll() {
	const serialPorts = (navigator.serial) ? await navigator.serial.getPorts() : [];
	const usbDevices = (navigator.usb) ? await navigator.usb.getDevices() : [];
	const hidDevices = (navigator.hid) ? await navigator.hid.getDevices() : [];
	// render these lists to the page with "Close"/"Release" buttons for each
}
listAll();
</script>
