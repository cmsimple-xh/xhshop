function submitXHSForm(name) {
	window.document.forms[name].submit();
	return true;
}

function xhsDisableShipping() {
	var inputs;
	inputs = window.document.getElementById('xhsShippingDetails').getElementsByTagName('input');
	var i;
	for (i = 0; i < inputs.length; i++) {
		inputs[i].disabled = true;
	}

}

function xhsEnableShipping() {
	var inputs;
	inputs = window.document.getElementById('xhsShippingDetails').getElementsByTagName('input');
	var i;
	for (i = 0; i < inputs.length; i++) {
		inputs[i].disabled = false;
	}
}

function xhsChangePic(pic, path, elementID) {
	var test = pic.split('.');
	var html = '';

	if (test.length == 2) {
		var extensions = new Array('jpg', 'jpeg', 'png', 'tif', 'gif', 'tiff', 'svg');
		var index = extensions.indexOf(test[1]);
		if (index > -1)
			html = '<img src="' + path + pic + '" />';
	}
	window.document.getElementById(elementID).innerHTML = html;
}

function xhsAssureDelete(string) {

	return confirm('Really delete "' + string + '"?');
}

function test(test) {
	alert(test);
}
