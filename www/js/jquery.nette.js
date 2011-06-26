/**
 * Nette Framework
 *
 * Copyright (c) 2004, 2009 David Grudl (http://davidgrudl.com)
 *
 * This source file is subject to the "Nette license" that is bundled
 * with this package in the file license.txt.
 *
 * For more information please see http://nettephp.com
 *
 * @version    $Id: nette.js 206 2009-02-05 02:33:13Z david@grudl.com $
 */

var nette = {

	// public
	lastError: null,

	spinnerId: "spinner",

	processing: 0,

	debug: true,

	result: {},

	action: function(action, sender)
	{
		if (this.processing > 0) return true;

		if (typeof action === 'object') {
			sender = action;
			action = sender.href;
		}

		this.result = {};

		// create new AJAX request
		this.initAjax();
		if (!this.ajax) return false;

		action += (action.indexOf('?') == -1) ? '?' : '&';
		if (typeof(this.state) === 'object') {
			action += this.buildQuery(this.state, '', '');
		}

		// create process indicator
		try {
			var img = document.getElementById(this.spinnerId);
			if (sender && img) {
				this.spinner = img.cloneNode(true);
				this.spinner.style.display = 'inline';
				sender.parentNode.insertBefore(this.spinner, sender.nextSibling);
			}
		} catch (e) {
		}

		try {
			var url = action + '-r=' + Math.random();
			var query = null;

			this.ajax.open("POST", url, true);
			this.ajax.setRequestHeader("X-Requested-With", "XMLHttpRequest");
			//this.ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
			this.ajax.onreadystatechange = function() { nette.ajaxHandler(); }
			this.ajax.send(query);
			this.processing = 1;
			return true;

		} catch (e) {
			return false;
		}
	},



	onSuccess: function()
	{
		if (this.result.error) {
			this.onError(this.result.error.message);
			return;
		}

		if (this.result.redirect) {
			var url = this.result.redirect;
			if (this.debug) {
				alert(url);
			}
			document.location = url;
			return;
		}

		if (this.result.state) {
			this.state = this.result.state;
		}

		if (this.result.events) {
			for (var id in this.result.events) {
				this.handleEvent(this.result.events[id]);
			}
		}

		if (this.result.snippets) {
			for (var id in this.result.snippets) {
				this.updateSnippet(id, this.result.snippets[id]);
			}
		}

		this.result = {};

		if ($('input.datepicker').get() != '') {
			$('input.datepicker').datepicker({ duration: 'fast' });
		}
	},


	onError: function(message)
	{
		this.lastError = message;

		if (this.debug) {
			alert(message);
		}
	},


	updateSnippet: function(id, html)
	{
		var el = document.getElementById(id);
		if (el) el.innerHTML = html;
	},

	handleEvent: function(args)
	{
		if (this.debug) {
			//alert(event + '(' + args + ')');
		}
		var event = args.shift();
		var obj = event.split('.');
		obj.pop();
		obj = obj.join('.');
		eval(event + '.apply(' + obj + ', args);');
	},


	// private
	state: null,

	spinner: null,

	ajax: null,


	initAjax: function()
	{
		this.ajax = false;

		if (typeof XMLHttpRequest !== 'undefined') {
			this.ajax = new XMLHttpRequest();
		}

		if (!this.ajax && window.ActiveXObject) {
			try {
				this.ajax = new ActiveXObject("Msxml2.XMLHTTP");
			} catch (e) {
				try {
					this.ajax = new ActiveXObject("Microsoft.XMLHTTP");
				} catch (e) {
					this.ajax = false;
				}
			}
		}
	},


	ajaxHandler: function()
	{
		if (this.ajax.readyState == 4) {
			// remove process indicator
			if (this.spinner) {
				this.spinner.parentNode.removeChild(this.spinner);
				this.spinner = null;
			}

			if (this.ajax.status === 200) {
				eval('this.result = ' + this.ajax.responseText);
				this.onSuccess();

			} else  {
				this.onError(this.ajax.status + " " + this.ajax.statusText + "\n\n" + this.ajax.responseText);
			}

			this.processing -= 1;
			this.ajax = false;
		}
	},


	buildQuery: function(data, prefix, postfix)
	{
		var s = '';
		for (var key in data) {
			if (typeof(data[key]) === 'object') {
				s += this.buildQuery(data[key], prefix + encodeURIComponent(key) + postfix + '%5B', '%5D');
			} else {
				if (data[key] === true || data[key] === false) {
					data[key] = data[key] ? '1' : '0';
				}
				s += prefix + encodeURIComponent(key) + postfix + '=' + encodeURIComponent((data[key].toString())) + '&';
			}
		}
		return s.replace(/%20/g, '+');
	},

	formAction: function(form, event)
	{
        if (this.processing > 0) return true;

        this.result = {};

        // create new AJAX request
        this.initAjax();
        if (!this.ajax) return false;

        var sender = document.activeElement || event.explicitOriginalTarget;
        var action = form.action;

        action += (action.indexOf('?') == -1) ? '?' : '&';
        if (typeof(this.state) === 'object') {
                action += this.buildQuery(this.state, '', '');
        }

        // create process indicator
        try {
				// user-defense reload :)
				$('#working').fadeTo(300, 0.5);
                /*/
				var img = document.getElementById(this.spinnerId);
                if (sender && img) {
                        this.spinner = img.cloneNode(true);
                        this.spinner.style.display = 'inline';
                        sender.parentNode.insertBefore(this.spinner, sender.nextSibling);
                }/**/
        } catch (e) {
        }

        try {
                var url = action + '-r=' + Math.random();
                var query = this.buildFormQuery(form, sender);

                this.ajax.open("POST", url, true);
                this.ajax.setRequestHeader("X-Requested-With", "XMLHttpRequest");
                this.ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
                this.ajax.onreadystatechange = function() { nette.ajaxHandler(); }
                this.ajax.send(query);
                this.processing = 1;
				$('#working').fadeOut(300);
                return true;

        } catch (e) {
                return false;
        }
	},

	buildFormQuery: function(form, sender)
	{
        var s = '';
        var amp = '';

		var els = $('#'+form.id+' input,select,textarea').get();
		for (var i = 0; i < els.length; i++) {
                switch (els[i].tagName) {
                        case 'INPUT':
                                switch (els[i].type) {
                                        case 'text':
                                        case 'password':
                                        case 'hidden':
                                                s += amp + encodeURIComponent(els[i].name) +
                                                        "=" + encodeURIComponent(els[i].value);
                                                break;

                                        case 'checkbox':
                                        case 'radio':
                                                if (els[i].checked)
                                                        s += amp + encodeURIComponent(els[i].name) +
                                                                "=" + encodeURIComponent(els[i].value);
                                                break;
                                }
                                break;

                        case 'TEXTAREA':
                                s += amp + encodeURIComponent(els[i].name) +
                                        "=" + encodeURIComponent(els[i].value);
                                break;

                        case 'SELECT':
                                s += amp + encodeURIComponent(els[i].name) +
                                        "=" + encodeURIComponent(els[i].options[els[i].selectedIndex].value);
                                break;
                }
                amp = '&';
        }

        if (sender != null && sender.type != 'checkbox') {
                s += '&' + encodeURIComponent(sender.name) + '=' + encodeURIComponent(sender.value);
        }

        return s.replace(/%20/g, '+');
	}
}
