document.addEventListener('DOMContentLoaded', function(){
	// settings
	var autoLoadMore = false;

	// states
	var selectedIconSlug = null;
	var prevSelectedIconSlug = null;

	// nodes
	var wpbody 				= document.querySelector('#wpbody');
	var preview 			= document.querySelector('#shortcode-preview');
	var copyShortcodeBtn 	= document.querySelector('#copy-shortcode');
	var copyShortcodeMsg 	= document.querySelector('#copy-shortcode-msg');
	var placeholder 		= document.querySelector('#placeholder-preview');
	var copyPlaceholderBtn 	= document.querySelector('#copy-placeholder');
	var copyPlaceholderMsg 	= document.querySelector('#copy-placeholder-msg');
	var iconPreview 		= document.querySelector('#icon-preview');
	var icons 				= document.querySelectorAll('ul.simpleicons-list li');
	var iconListWrapper 	= document.querySelector('.simpleicons-list-wrapper');
	var iconList 			= document.querySelector('ul.simpleicons-list');
	var iconListOrigin		= iconList.cloneNode(true);
	var search 				= document.querySelector('#simple-icons-search');					
	var resultsCounter 		= document.querySelector('.search-results');
	var loadElem 			= document.createElement('div');

	// variables
	var currentPage			= 0;
	var currentValue		= undefined;
	var changeTimer 		= false;
	var loadMoreTriggered	= false;
	var moreItemsExist		= false;
	loadElem.classList.add('simpleicons-loader');

	var loadMoreWrapperElem	= document.createElement('div');
	loadMoreWrapperElem.classList.add('simpleicons-loadmore-wrapper');
	var loadMoreElem		= document.createElement('div');
	loadMoreElem.classList.add('button');
	loadMoreElem.innerHTML = 'Load More';
	loadMoreWrapperElem.appendChild(loadMoreElem);


	search.addEventListener('input', function(){
		debug('Input changed to: ' + this.value);
		var value = this.value
		if (changeTimer !== false) clearTimeout(changeTimer);
		changeTimer = setTimeout(function(){
			debug('Inside timeout function for search: \"' + value + '\"');
			currentPage = 0;
			displayIcons(value);
			changeTimer = false;
			currentValue = value;
		}, 300);
	});

	loadMoreElem.addEventListener('click', function() {
		loadMoreIcons();
	});

	if (autoLoadMore === true) {
		window.onscroll = function(ev) {
		    if ((window.innerHeight + window.pageYOffset) >= wpbody.offsetHeight) {
		        debug('Bottom of page reached.');
		        loadMoreIcons();
		    }
		};
	}

	function loadMoreIcons() {
		if (loadMoreTriggered === false && moreItemsExist === true) {
			loadMoreTriggered = true;
			debug('Load more triggered.');
			currentPage++;
			displayIcons(currentValue);
		}
	}


	function displayIcons(search) {
		var data = {
			'action': 'simpleicons_search_icons',
			'search': search,
			'page': currentPage
		};
		
		// actions performed before results return (clearing)	
		if (data.page === 0) resultsCounter.innerHTML = '';
		if (data.page === 0) iconList.innerHTML = '';
		toggleLoadingIcon(true);
		toggleLoadMoreBtn(false);

		debug('Request initiated for search: \"' + search + '\", page: \"' + currentPage + '\"');
		jQuery.post(ajaxurl, data, function(results) {
			results = JSON.parse(results);
			if (currentValue == search) {
				results.search_term = search;
				debug(results);
				
				// page === 0 means a new search, not the load more button
				if (data.page === 0) iconList.innerHTML = '';

				if (results.icons && results.icons.length !== 0) {
					for (var i = 0; i < results.icons.length; i++) {
						// create icon list item element
						listitem = document.createElement('li');
						listitem.dataset.icontitle = results.icons[i]['slug'];
						listitem.innerHTML = results.icons[i]['svg'];
						setClickHandler(listitem);

						// add to the ul list
						iconList.appendChild(listitem);
					}
					if (currentValue === '') {
						resultsCounter.innerHTML = 'All ' + results.total_icons + ' icons';
					} else {
						resultsCounter.innerHTML = results.total_icons + ' matching results';
					}
					toggleLoadingIcon(false);
					toggleLoadMoreBtn(results.more_items);
				} else {
					iconList.innerHTML = 'No icons found. <a target="_blank" title="Request an icon" href="https://github.com/simple-icons/simple-icons/issues?q=is%3Aopen+is%3Aissue+label%3A%22new+icon%22">Request an icon</a>';
					toggleLoadingIcon(false);
					toggleLoadMoreBtn(false);
				}
				loadMoreTriggered = false;
				debug('Request finished for search: \"' + search + '\", page: \"' + currentPage + '\"');
			} else {
				debug('Cancelled search for: \"' + search + '\"');
			}
		});
	} displayIcons(); // initial display

	function toggleLoadingIcon($toggle) {
		if ($toggle) {
			iconListWrapper.appendChild(loadElem);
		} else {
			if (iconListWrapper.contains(loadElem))
			iconListWrapper.removeChild(loadElem);
		}
	}

	function toggleLoadMoreBtn($toggle) {
		if ($toggle) {
			iconListWrapper.appendChild(loadMoreWrapperElem);
			moreItemsExist = true;
		} else {
			if (iconListWrapper.contains(loadMoreWrapperElem)) {
				iconListWrapper.removeChild(loadMoreWrapperElem);
			}
			moreItemsExist = false;
		}
	}

	function setClickHandler(icon) {
		icon.addEventListener('click', function(){
			updateSelectedIconSlug(this.dataset.icontitle);
			updateUI();							
		});
	}

	function updateSelectedIconSlug(slug) {
		prevSelectedIconSlug = selectedIconSlug;
		selectedIconSlug = slug;
	}

	function updateUI() {
		var prevIcon = document.querySelector(`[data-icontitle="${prevSelectedIconSlug}"]`);
		var currentIcon = document.querySelector(`[data-icontitle="${selectedIconSlug}"]`);

		if (prevIcon) {
			prevIcon.classList.remove('selected');
		}

		preview.textContent = `[simple_icon name="${selectedIconSlug}"]`;
		copyShortcodeMsg.textContent = '';
		placeholder.textContent = `#${selectedIconSlug}#`;
		copyPlaceholderMsg.textContent = '';

		currentIcon.classList.add('selected');

		iconPreview.innerHTML = '';
		$svg = currentIcon.firstChild.cloneNode(true);
		iconPreview.appendChild($svg);

		window.scrollTo(0, 0);
	}

	copyShortcodeBtn.addEventListener('click', function(){
		var text = preview.textContent;
		copyTextToClipboard(copyShortcodeMsg, text);
	});

	copyPlaceholderBtn.addEventListener('click', function(){
		var text = placeholder.textContent;
		copyTextToClipboard(copyPlaceholderMsg, text);
	});


	function fallbackCopyTextToClipboard(message, text) {
		var textArea = document.createElement("textarea");
		message.classList.remove('fadeOut');
		textArea.value = text;
		preview.appendChild(textArea);
		textArea.focus();
		textArea.select();

		try {
			var successful = document.execCommand('copy');
			var msg = successful ? 'copied to clipboard!' : 'error';
			message.textContent = msg;
			message.classList.add('fadeOut');
		} catch (err) {
			message.textContent = 'error: ' + err;
		}

		preview.removeChild(textArea);
	}
	function copyTextToClipboard(message, text) {
		if (!navigator.clipboard) {
			fallbackCopyTextToClipboard(message, text);
			return;
		}
		navigator.clipboard.writeText(text).then(function() {
			message.textContent = 'copied to clipboard!';
			message.classList.add('fadeOut');
		}, function(err) {
			message.textContent = 'error: ' + err;
		});
	}
	function debug(message) {
		if (simple_icons_settings.debug) {
			console.log(message);
		}
	}
});