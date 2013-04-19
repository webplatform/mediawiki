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
