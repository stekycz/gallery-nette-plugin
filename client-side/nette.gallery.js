var gallery = {
	init: function (relation) {
		$("a[rel="+relation+"]").fancybox({
			'transitionIn'		: 'none',
			'transitionOut'		: 'none',
			'titlePosition' 	: 'over',
			'titleFormat'       : function(title, currentArray, currentIndex, currentOpts) {
				return '<span id="fancybox-title-over">Image ' +  (currentIndex + 1) + ' / ' + currentArray.length + ' ' + title + '</span>';
			}
		});
	},
	
	update: function (eo, snippetId) {
		var relationValue = gallery.getRelationValue(snippetId);
		gallery.init(relationValue);
	},
	
	getRelationValue: function (id) {
		var snippet = $('#'+id);
		var table = snippet.find('.items');
		return table.attr('id');
	},
	
	updateSnippet: function (id, html) {
		var el = $('#'+id);
		if (el) {
			el.html(html);
			el.trigger('galleryUpdated', [id]);
		}
	}
};

if (jQuery.nette) {
	jQuery.nette.updateSnippet = gallery.updateSnippet;
}

if (nette) {
	nette.updateSnippet = gallery.updateSnippet;
}

$(document).ready(function (eo) {
	var table = $('.items');
	if (table) {
		var snippet = table.parent('div');
		if (snippet) {
			snippet.bind('galleryUpdated', gallery.update);
		}
		gallery.init(table.attr('id'));
	}
});
