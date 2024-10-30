if (typeof(djConfig) === 'undefined') {
	var djConfig = {};
}
(function() {
	var base_url = "http://incil.info/";

	djConfig.afterOnLoad = true;
	djConfig.usePlainJson = true;
	djConfig.parseOnLoad = false;

	if (typeof(google) === 'object' && typeof(google.load) !== 'undefined') {
		if (typeof(dojo) === 'undefined') {
			google.load('dojo', '1.5');
		}

		google.setOnLoadCallback(function() {
			dojo.addOnLoad(incilinfo);
		});
	} else if (typeof(dojo) === 'object' && typeof(dojo.addOnLoad) !== 'undefined'){
		dojo.addOnLoad(incilinfo);
	} else {
		djConfig.addOnLoad = incilinfo;
		var j = document.getElementsByTagName("head")[0].appendChild(document.createElement("script"));
		j.type = "text/javascript";
		j.src = "http://ajax.googleapis.com/ajax/libs/dojo/1.5/dojo/dojo.xd.js";
	}

	function incilinfo () {
		dojo.addClass(dojo.body(), "tundra");

		var d = document.getElementsByTagName("head")[0].appendChild(document.createElement("link"));
		d.type = "text/css";
		d.rel = "stylesheet";
		d.href = "http://ajax.googleapis.com/ajax/libs/dojo/1.5/dijit/themes/tundra/tundra.css";

		var s = document.getElementsByTagName("head")[0].appendChild(document.createElement("link"));
		s.type = "text/css";
		s.rel = "stylesheet";
		s.href = base_url + "eklentiler/stil.css";

		var api_url = base_url + "api";

		dojo.require('dojo.io.script');
		dojo.require("dijit.Tooltip");

		dojo.addOnLoad(function() {
			var ayetler = [];
			dojo.query(".incil_ayet").forEach(function(ayet){
				var addtips = new dojo.Deferred();
				var query = dojo.hasAttr(ayet, 'incilReferans') ? dojo.attr(ayet, 'incilReferans') : ayet.innerHTML;
				addtips.then(function(sonuc){
					return new dijit.Tooltip({
						connectId: [ayet],
						position: ['below'],
						label: sonuc.tooltip
					});
				});
				ayetler.push({query: query, tipper: addtips});
			});

			dojo.io.script.get({
				url: api_url,
				content: { "queries[]": dojo.map(ayetler, function(ayet) { return ayet.query; }) },
				callbackParamName: 'callback',
				load: function(sonuclar) {
					dojo.forEach(sonuclar, function(sonuc, i) {
						ayetler[i].tipper.callback(sonuc);
					});
				}
			});
		});
	}
}());
