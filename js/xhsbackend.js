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

if ("visibilityState" in document) {
	document.addEventListener("DOMContentLoaded", function () {
		var each = Array.prototype.forEach;
	
		function rowOfForm(form) {
			var element = form.parentNode;
	
			while (element.tagName !== "TR") {
				element = element.parentNode;
			}
			return element;
		}
	
		function serialize(form) {
			var params = [];
			each.call(form.elements, function (element) {
				if (!element.name) {
					return;
				}
				params.push(element.name + "=" + encodeURIComponent(element.value));
			});
			return params.join("&");
		}
	
		function submit(form) {
			var request = new XMLHttpRequest;
			request.open("POST", form.action, true);
			request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
			request.setRequestHeader("X-Requested-With", "XMLHttpRequest");
			request.send(serialize(form));
		}
	
		function fixSwapIds() {
			var forms = document.getElementsByClassName("xhsMoveUp");
			var form;
			if ((form = forms[0])) {
				form.xhsProductSwapID.value = "";
				form.style.display = "none";
			}
			for (var i = 1; i < forms.length; i++) {
				form = forms[i];
				form.xhsProductSwapID.value = forms[i-1].xhsProductID.value;
				form.style.display = "";
			}
			var forms = document.getElementsByClassName("xhsMoveDown");
			for (var i = 0; i < forms.length - 1; i++) {
				form = forms[i];
				form.xhsProductSwapID.value = forms[i+1].xhsProductID.value;
				form.style.display = "";
			}
			if ((form = forms[forms.length-1])) {
				form.xhsProductSwapID.value = "";
				form.style.display = "none";
			}
		}
	
		each.call(document.getElementsByClassName("xhsMoveUp"), function (form) {
			form.addEventListener("submit", function (event) {
				var thisRow = rowOfForm(this);
				submit(this);
				var thatRow = thisRow.previousElementSibling;
				thisRow.parentNode.insertBefore(thisRow, thatRow);
				fixSwapIds();
				event.preventDefault();
			});
		});
	
		each.call(document.getElementsByClassName("xhsMoveDown"), function (form) {
			form.addEventListener("submit", function (event) {
				var thisRow = rowOfForm(this);
				submit(this);
				var thatRow = thisRow.nextElementSibling;
				thisRow.parentNode.insertBefore(thatRow, thisRow);
				fixSwapIds();
				event.preventDefault();
			});
		});
	});
}
