/**
 * Main JS - Haus of Crunch Theme
 * Handles filter panel toggle, header mobile menu, and accessibility
 */
(function($){
	'use strict';
	
	$(document).ready(function(){
		// Filter Panel Toggle (Mobile)
		$('.hoc-filter-toggle').on('click', function(e){
			e.preventDefault();
			var $toggle = $(this);
			var $sidebar = $('.hoc-shop-sidebar');
			var isExpanded = $toggle.attr('aria-expanded') === 'true';
			
			// Toggle sidebar (which contains the filter panel)
			$sidebar.toggleClass('is-open');
			$toggle.attr('aria-expanded', !isExpanded);
			
			// Close on escape
			if (!isExpanded) {
				$(document).on('keydown.filterPanel', function(e){
					if (e.key === 'Escape' && $sidebar.hasClass('is-open')) {
						$sidebar.removeClass('is-open');
						$toggle.attr('aria-expanded', 'false').focus();
						$(document).off('keydown.filterPanel');
					}
				});
			} else {
				$(document).off('keydown.filterPanel');
			}
		});
		
		// Close filter panel when clicking outside (mobile only)
		if ($(window).width() <= 640) {
			$(document).on('click.filterPanel', function(e){
				if (!$(e.target).closest('.hoc-shop-sidebar, .hoc-filter-toggle').length && $('.hoc-shop-sidebar').hasClass('is-open')) {
					$('.hoc-shop-sidebar').removeClass('is-open');
					$('.hoc-filter-toggle').attr('aria-expanded', 'false');
				}
			});
		}

		// Header Mobile Menu Toggle
		var $menuToggle = $('.hoc-header__menu-toggle');
		var $mobileMenu = $('.hoc-header__mobile-menu');
		var $body = $('body');

		if ($menuToggle.length && $mobileMenu.length) {
			$menuToggle.on('click', function(e){
				e.preventDefault();
				var isExpanded = $(this).attr('aria-expanded') === 'true';
				
				// Toggle menu
				$mobileMenu.toggleClass('is-open');
				$(this).attr('aria-expanded', !isExpanded);
				$mobileMenu.attr('aria-hidden', isExpanded);
				
				// Toggle body scroll lock
				if (!isExpanded) {
					$body.addClass('hoc-menu-open');
				} else {
					$body.removeClass('hoc-menu-open');
				}
				
				// Close on escape
				if (!isExpanded) {
					$(document).on('keydown.mobileMenu', function(e){
						if (e.key === 'Escape' && $mobileMenu.hasClass('is-open')) {
							$mobileMenu.removeClass('is-open');
							$menuToggle.attr('aria-expanded', 'false');
							$mobileMenu.attr('aria-hidden', 'true');
							$body.removeClass('hoc-menu-open');
							$menuToggle.focus();
							$(document).off('keydown.mobileMenu');
						}
					});
				} else {
					$(document).off('keydown.mobileMenu');
				}
			});

			// Close mobile menu when clicking outside (mobile only)
			if ($(window).width() < 1024) {
				$(document).on('click.mobileMenu', function(e){
					if (!$(e.target).closest('.hoc-header__mobile-menu, .hoc-header__menu-toggle').length && 
						$mobileMenu.hasClass('is-open')) {
						$mobileMenu.removeClass('is-open');
						$menuToggle.attr('aria-expanded', 'false');
						$mobileMenu.attr('aria-hidden', 'true');
						$body.removeClass('hoc-menu-open');
					}
				});
			}

			// Mobile submenu toggle
			$('.hoc-header__mobile-menu-toggle').on('click', function(e){
				e.preventDefault();
				var $button = $(this);
				var $menuItem = $button.closest('.hoc-header__mobile-menu-item');
				var isExpanded = $button.attr('aria-expanded') === 'true';
				
				// Toggle submenu
				$menuItem.toggleClass('is-open');
				$button.attr('aria-expanded', !isExpanded);
			});

			// Close mobile menu on window resize (if desktop)
			$(window).on('resize', function(){
				if ($(window).width() >= 1024 && $mobileMenu.hasClass('is-open')) {
					$mobileMenu.removeClass('is-open');
					$menuToggle.attr('aria-expanded', 'false');
					$mobileMenu.attr('aria-hidden', 'true');
					$body.removeClass('hoc-menu-open');
				}
			});
		}

		// Announcement Bar Dismissible Functionality
		var $announcementBar = $('.hoc-announcement-bar--dismissible');
		
		if ($announcementBar.length) {
			$announcementBar.each(function(){
				var $bar = $(this);
				var announcementId = $bar.data('announcement-id') || $bar.attr('id');
				
				if (!announcementId) {
					return;
				}
				
				// Check if announcement was already dismissed
				var dismissed = localStorage.getItem('hoc_announcement_dismissed_' + announcementId);
				
				if (dismissed === 'true') {
					$bar.addClass('hoc-announcement-bar--hidden');
					return;
				}
				
				// Handle close button click
				$bar.find('.hoc-announcement-bar__close').on('click', function(e){
					e.preventDefault();
					
					// Save dismissal to localStorage
					localStorage.setItem('hoc_announcement_dismissed_' + announcementId, 'true');
					
					// Animate out
					$bar.addClass('hoc-announcement-bar--dismissing');
					
					// Remove from DOM after animation
					setTimeout(function(){
						$bar.addClass('hoc-announcement-bar--hidden');
					}, 300);
				});
			});
		}
	});
})(jQuery);
  