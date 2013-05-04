function setupSearchField() {
    var searchInput = $('#searchInput'),
        defaultValue = 'Search...';

    searchInput.focus(function () {
        if (searchInput.val() == defaultValue) {
            searchInput.val('');    
        }
    });

    searchInput.blur(function () {
        if (searchInput.val() === '') {
            searchInput.val(defaultValue);
        }
    });
}

function init() {
    setupSearchField();
    
    if (document.querySelectorAll && document.body.addEventListener) {
    	var dropdowns = document.querySelectorAll('.dropdown');
    	
    	for (var i=0, dropdown; dropdown=dropdowns[i++];) {
    		dropdown.addEventListener('focus', function () {
    			this.className += ' focus';
    		}, true);
    		
    		dropdown.addEventListener('blur', function () {
    			this.className = this.className.replace(/\s+focus\b/, ' ');
    		}, true);
    	}
    	
    	// Syntax highlighting for examples with a language
    	var langs = document.querySelectorAll('.example > p > .language');
    	
    	for (var i=0, lang; lang = langs[i++];) {
    		var pre = lang.parentNode.parentNode.querySelector('pre');
    		
    		var code = document.createElement('code');
    		code.className = 'language-' + {
    			'JavaScript': 'javascript',
    			'HTML': 'markup',
    			'CSS': 'css'
    		}[lang.textContent];
    		
    		code.innerHTML = pre.innerHTML;
    		pre.innerHTML = '';
    		pre.appendChild(code);
    	}
    	
    	var prism = document.createElement('script');
    	prism.src = '/t/skins/webplatform/prism.js';
    	document.head.appendChild(prism);
    	prism.onload = function () {
    		window.Prism && Prism.highlightAll();
    	}
    }
}

$(document).ready(init);

document.addEventListener && document.addEventListener('DOMContentLoaded', function(){
	var page = document.getElementById('mw-content-text');
	var headings = Array.prototype.slice.apply(page.querySelectorAll('h2, h3, h4, h5, h6'));
	
	var ol = document.createElement('ol'), li, rootOl = ol;
	
	for (var i=0, h; h=headings[i++];) {
		var level = hLevel(h);
		
		if (level > previousLevel) {
			ol = li.appendChild(document.createElement('ol'));
		}
		else if (level < previousLevel) {
			ol = ol.parentNode.parentNode;
		}
		
		li = tocItem(h);
		
		if (li) {
			ol.appendChild(li);
		}
		
		var previousLevel = level;
	}
	
	function tocItem(h) {
		var li = document.createElement('li'),
		    a = document.createElement('a');
		
		var id, text;
		
		var headline = h.querySelector('.mw-headline[id]');
			
		if (headline) {
			id = headline.id;
			text = headline.textContent;
		}
		else {
			id = h.id;
			text = h.firstChild.textContent || h.textContent;
			
			if (!id) {
				id = text.replace(/\s+/g, '-');
				
				if (document.getElementById(id)) {
					// Id already exists
					id += '-2';
				}
				
				h.id = id;
			}
		}
		
		a.textContent = text;
		a.href = '#' + id;
		
		li.appendChild(a);
		
		return li;
	}
	
	function hLevel(h) {
		return +h.nodeName.match(/h(\d)/i)[1];
	}
	
	var toc = document.createElement('aside');
	toc.id = 'sidebar';
	toc.className = 'custom-toc';
	
	var tocH = document.createElement('h2');
	tocH.id = 'sidebar-title';
	tocH.innerHTML = 'Contents';
	
	toc.appendChild(tocH);
	toc.appendChild(rootOl);
	
	page.insertBefore(toc, page.firstChild);
});