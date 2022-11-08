/**
 * Print Helper
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

POS.Printer = {

	printLocalBrowser: function() {},

	/**
	 * Download the Document as PDF and then send to our HTTP Print Queue
	 */
	printLocalNetwork: function(pdf_url, printer_url)
	{
		console.log('POS.Printer.printLocalNetwork()');

		// Big AJAX
		$.ajax({
			type: 'POST',
			url: pdf_url,
			data: {
				a: 'send-print',
			},
			dataType: 'binary',
			xhrFields: {
				responseType: 'blob',
			},
			success: function(body, ret) {

				// Then another Big AJAX
				var FD = new FormData();
				FD.append('a', 'print-file');
				FD.append('file', body);

				$.ajax({
					type: "POST",
					url: printer_url,
					cache: false,
					contentType: false,
					processData: false,
					data: FD,
					xhrFields:{
						responseType: 'blob'
					},
				});
			}
		});

	},

	printServerConnect: function() {},

};
