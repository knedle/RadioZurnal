/**
 * AJAX Nette Framwork plugin for jQuery
 *
 * @copyright  Copyright (c) 2009, 2010 Jan Marek
 * @copyright  Copyright (c) 2009, 2010 David Grudl
 * @license    MIT
 * @link       http://nette.org/cs/extras/jquery-ajax
 */

/*
if (typeof jQuery != 'function') {
	alert('jQuery was not loaded');
}
*/

(function($) {

	$.nette = {
		success: function(payload)
		{
			// redirect
			if (payload.redirect) {
				window.location.href = payload.redirect;
				return;
			}

			// state
			if (payload.state) {
				$.nette.state = payload.state;
			}

			// snippets
			if (payload.snippets) {
				for (var i in payload.snippets) {
					$.nette.updateSnippet(i, payload.snippets[i]);
				}
			}

			// change URL (requires HTML5)
			if (window.history && history.pushState && $.nette.href) {
				history.pushState({href: $.nette.href}, '', $.nette.href);
			}
		},

		updateSnippet: function(id, html)
		{
			$('#' + id).html(html);
		},

		// create animated spinner
		createSpinner: function(id)
		{
			return this.spinner = $('<div></div>').attr('id', id ? id : 'ajax-spinner').ajaxStart(function() {
				$(this).show();

			}).ajaxStop(function() {
				$(this).hide().css({
					position: 'fixed',
					left: '50%',
					top: '50%'
				});

			}).appendTo('body').hide();
		},

		// current page state
		state: null,
		href: null,

		// spinner element
		spinner: null
	};


})(jQuery);



jQuery(function($) {
	// HTML 5 popstate event
	$(window).bind('popstate', function(event) {
		$.nette.href = null;
                if (event.originalEvent.state != null) {
                    $.post(event.originalEvent.state.href, $.nette.success);
                }
	});

	$.ajaxSetup({
		success: $.nette.success,
		dataType: 'json'
	});

	$.nette.createSpinner();

	// apply AJAX unobtrusive way
	$('a.ajax').live('click', function(event) {
		event.preventDefault();
		if ($.active) return;

		$.post($.nette.href = this.href, $.nette.success);

		$.nette.spinner.css({
			position: 'absolute',
			left: event.pageX,
			top: event.pageY
		});
	});

});


/**
 * AJAX form plugin for jQuery
 *
 * @copyright  Copyright (c) 2009 Jan Marek
 * @license    MIT
 * @link       http://nettephp.com/cs/extras/ajax-form
 * @version    0.1
 */

jQuery.fn.extend({
        ajaxSubmit: function (callback) {
                var form;
                var sendValues = {};

                // submit button
                if (this.is(":submit")) {
                        form = this.parents("form");
                        sendValues[this.attr("name")] = this.val() || "";

                // form
                } else if (this.is("form")) {
                        form = this;

                // invalid element, do nothing
                } else {
                        return null;
                }

                // validation
                if (form.get(0).onsubmit && !form.get(0).onsubmit()) return null;

                // get values
                var values = form.serializeArray();

                for (var i = 0; i < values.length; i++) {
                        var name = values[i].name;

                        // multi
                        if (name in sendValues) {
                                var val = sendValues[name];

                                if (!(val instanceof Array)) {
                                        val = [val];
                                }

                                val.push(values[i].value);
                                sendValues[name] = val;
                        } else {
                                sendValues[name] = values[i].value;
                        }
                }

                // send ajax request
                var ajaxOptions = {
                        url: form.attr("action"),
                        data: sendValues,
                        type: form.attr("method") || "get"
                };

                if (callback) {
                        ajaxOptions.success = callback;
                }

                return jQuery.ajax(ajaxOptions);
        }
});