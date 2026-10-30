$.noConflict();

jQuery(document).ready(function($) {

	"use strict";

	[].slice.call( document.querySelectorAll( 'select.cs-select' ) ).forEach( function(el) {
		new SelectFx(el);
	} );

	jQuery('.selectpicker').selectpicker;


	$('#menuToggle').on('click', function(event) {
		$('body').toggleClass('open');
	});

	$('.search-trigger').on('click', function(event) {
		event.preventDefault();
		event.stopPropagation();
		$('.search-trigger').parent('.header-left').addClass('open');
	});

	$('.search-close').on('click', function(event) {
		event.preventDefault();
		event.stopPropagation();
		$('.search-trigger').parent('.header-left').removeClass('open');
	});

	// Highlight the sidebar item that matches the current admin page.
	var $adminMenu = $('#main-menu');
	if ($adminMenu.length) {
		var currentPath = window.location.pathname.replace(/\/+$/, '') || '/';
		var $bestLink = null;
		var bestMatchLength = -1;

		$adminMenu.find('a[href]').each(function() {
			var href = $(this).attr('href');
			if (!href || href === '#' || href.indexOf('javascript:') === 0) {
				return;
			}

			var linkPath;
			try {
				linkPath = new URL(href, window.location.origin).pathname.replace(/\/+$/, '') || '/';
			} catch (error) {
				return;
			}

			var isMatch = currentPath === linkPath
				|| (linkPath !== '/admin' && currentPath.indexOf(linkPath + '/') === 0);

			if (isMatch && linkPath.length > bestMatchLength) {
				bestMatchLength = linkPath.length;
				$bestLink = $(this);
			}
		});

		if ($bestLink) {
			$bestLink.parent('li').addClass('active');
			$bestLink.parents('li.menu-item-has-children').addClass('active');
		}
	}

	// $('.user-area> a').on('click', function(event) {
	// 	event.preventDefault();
	// 	event.stopPropagation();
	// 	$('.user-menu').parent().removeClass('open');
	// 	$('.user-menu').parent().toggleClass('open');
	// });


});