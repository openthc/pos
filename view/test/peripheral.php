<?php
/**
 * Test Routines for:
 * WebSerial, WebUSB, WebHID and ... ?
 *
 * SPDX-License-Identifier: MIT
 */

?>


<div class="container mt-4">

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
	<button class="btn btn-primary" id="presentation-connect">Begin Test</button>
	 <pre id="presentation-output"></pre>
</section>

<!-- <section class="mb-4">
	<h2>Web-Somethign?</h2>
</section> -->

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

<script>
if (!('keyboard' in navigator)) {
	$('#keyboard-connect').removeClass('btn-primary');
	$('#keyboard-connect').addClass('btn-danger');
	$('#keyboard-connect').attr('disabled', true);
	$('#keyboard-output').append('<span class="text-danger">Keyboard API is not supported in this browser</span>');
} else {
	$('#keyboard-output').append('Keyboard Listener Attached');
	navigator.keyboard.lock(["KeyW", "KeyA", "KeyS", "KeyD"]);

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

<script>
async function listAll() {
	console.log('listAll');
	const serialPorts = (navigator.serial) ? await navigator.serial.getPorts() : [];
	const usbDevices = (navigator.usb) ? await navigator.usb.getDevices() : [];
	const hidDevices = (navigator.hid) ? await navigator.hid.getDevices() : [];
	// render these lists to the page with "Close"/"Release" buttons for each
}
listAll();
</script>
