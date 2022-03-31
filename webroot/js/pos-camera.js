/**
 * Camera Integration
 * @see https://www.damirscorner.com/blog/posts/20170901-DetectingCameraPermissionDialogInBrowser.html
 */

(function(window) {

'use strict';

var Camera = null;
var Camera_auth = 0;

window.OpenTHC = window.OpenTHC || {};
window.OpenTHC.Camera = {

	/**
	 *
	 */
	exists: function(callback)
	{
		let dev_list = navigator.mediaDevices;
		if (!dev_list || !dev_list.enumerateDevices) return false;
		dev_list.enumerateDevices().then(devices => {
			callback(devices.some(device => 'videoinput' === device.kind));
		});

	},

	/**
	 * Open the Camera
	 */
	open: function(callback) {

		if (!Camera) {
			navigator.mediaDevices
				.getUserMedia({ audio: false, video: true })
				.then(function(stream) {
					Camera = stream;
					callback(stream);
				})
				.catch(function(err) { console.log(err); });
		}

	},

	scan: function(callback)
	{
		// Read from the camera until we get something
		// something == PDF417, QR or C128

		// Wait for QR Code

		// But Also show the Scanning Video On the Screen Somwehere

	}

};

})(window);
