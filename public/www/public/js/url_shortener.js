var ifedkoUrlShortener = {

	name: "ifedkoUrlShortener",
	config: {
		requestUri: "http://url.ifedko.ru/short_url",
		urlInputName: "url",
		resultSelectorId: "result",
		debug: false,
	},

	urlInput: null,
	form: null,
	resultBlock: null,

	setup: function(config) {
		try {
			if (typeof config !== "undefined") {
				this.customizeConfig(config);
			}

			this.urlInput = this.getUrlInput();
			this.form = this.getForm(this.urlInput);
			this.resultBlock = this.getResultBlock();

			var self = this;

			this.form.onsubmit = function() {
				self.loadShortUrl();
				return false;
			};
		} catch (error) {
			this.log(error, "error");
			this.warn("Sorry, service is unavailable. Please try again later.");
		}
	},

	customizeConfig: function (config) {
		if (typeof config !== "object") {
			return;
		}

		for (var property in config) {
			if (this.config.hasOwnProperty(property)) {
				this.config[property] = config[property];
			}
		}
	},

	getUrlInput: function() {
		var inputs = document.getElementsByName(this.config.urlInputName);
		var urlInput = (typeof inputs[0] === "object") ? inputs[0] : null;
		if (urlInput === null) {
			throw "[" + this.name + "] Input for URL not found.";
		}

		return urlInput;
	},

	getForm: function(urlInput) {
		var node = urlInput;
		while (node.nodeName != 'FORM' && node.parentNode) {
			node = node.parentNode;
		}
		var form = (node.nodeName == 'FORM') ? node : null;
		if (form === null) {
			throw "[" + this.name + "] Form for submit not found";
		}

		return form;
	},

	getResultBlock: function() {
		var result = document.getElementById(this.config.resultSelectorId);
		if (result === null) {
			throw "[" + this.name + "] Result block not found";
		}

		return result;
	},

	getUrlInputValue: function() {
		var url = this.urlInput.value;
		if (url.length == 0 || url === null) {
			throw "Please input URL";
		}

		return url;
	},

	createRequest: function() {
		var request;

		if (window.XMLHttpRequest) {
			// code for IE7+, Firefox, Chrome, Opera, Safari
			request = new XMLHttpRequest();
		} else {
			// code for IE6, IE5
			//request = new ActiveXObject("Microsoft.XMLHTTP");
			throw "Update your browser for use UrlShortener service";
		}

		return request;
	},

	loadShortUrl: function() {
		try {
			var url = this.getUrlInputValue();
			var request = this.createRequest();

			var self = this;
			request.onreadystatechange = function () {
				if (request.readyState == XMLHttpRequest.DONE) {
					if (request.status == 200) {
						self.log(request.responseText);

						var json = JSON.parse(request.responseText);
						self.log(json);

						if (typeof json !== "object" || json.short_url.length == 0) {
							self.log("Response from service is not correct", "error");
						}

						self.addUrlToResult(json.short_url);
					} else {
						throw "No response from service";
					}
				}
			}

			request.open("GET", this.config.requestUri + '?url=' + url, true);
			request.send();
		} catch (error) {
			this.log(error, "info");
			this.warn(error);
		}
	},

	addUrlToResult: function(url) {
		//this.resultBlock.innerHTML = '<a href="' + url + '"' + 'target="_blank">' + url + "<\/a>";
		this.resultBlock.innerHTML = '<span>' + url + "<\/span>";
	},

	log: function (message, level) {
		if (this.config.debug === true) {
			switch (level) {
				case "error":
					console.error(message);
				break;
				case
					"info"
				:
				default:
					console.log(message);
					break;
			}
		}
	},

	warn: function (message) {
		alert(message);
	}
};

