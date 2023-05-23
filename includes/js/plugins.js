(function() {
	'use strict';
	window.addEventListener('DOMContentLoaded',
		() => {
			/**
			 * Handle rating star hover
			 */
			function hover(e) {
				let fill = true;
				for (let $star of this.parentElement.querySelectorAll('span.dashicons')) {
					if (fill) {
						$star.classList.remove('dashicons-star-empty');
						$star.classList.add('dashicons-star-filled');
						fill = e.target !== $star;
					} else {
						$star.classList.remove('dashicons-star-filled');
						$star.classList.add('dashicons-star-empty');
					}
				}
			}
			/**
			 * Display random stars for rating
			 */
			function init($element) {
				const random = Math.floor(Math.random() * Math.floor(2)); //Random from 0 to 2
				const stars = $element.querySelectorAll('span.dashicons');
				for (let i = 4; i > 0; i--) {
					if (i > 4 - random) {
						stars[i].classList.remove('dashicons-star-filled');
						stars[i].classList.add('dashicons-star-empty');
					} else {
						break;
					}
				}
			}
			// Get all rating stars for all plugins and run loop for each plugin
			for (let $ratingStars of document.querySelectorAll('.pluginsclub-rating-stars')) {
				init($ratingStars); // Random stars
				$ratingStars.addEventListener('mouseover', hover);
			}
		});
}());