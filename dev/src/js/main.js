(function() {

	find_videos();
	media_playing();
	on_dom_change();


	/**
	 * Find videos.
	 *
	 * @since 0.2.0
	 */
	function find_videos() {
		const videos = document.querySelectorAll( '.sllv-video' );

		if ( videos.length > 0 ) {
			videos.forEach( ( video ) => {
				if ( ! video.classList.contains( '-state_enabled' ) ) {
					setup_video( video );
				}
			} );
		}
	}


	/**
	 * Observe DOM changes to find new videos dynamically.
	 * Replaces old jQuery ajaxComplete logic.
	 *
	 * @since 1.4.0
	 */
	function on_dom_change() {
		let timer;

		const observer = new MutationObserver( () => {
			// Clear the timer on every new mutation.
			clearTimeout( timer );

			// Set a new timer to run find_videos after DOM calms down.
			timer = setTimeout( find_videos, 300 );
		} );

		observer.observe( document.body, {
			childList: true, // Listen only to added/removed elements.
			subtree:   true, // Listen to changes deep inside the body.
		} );
	}


	/**
	 * Do some actions if HTML media starts playing.
	 * Uses event delegation on the capture phase to handle dynamically added media.
	 *
	 * @since 0.9.0
	 */
	function media_playing() {
		document.addEventListener( 'play', ( event ) => {
			const tag_name = event.target.tagName;

			if ( 'VIDEO' === tag_name || 'AUDIO' === tag_name ) {
				stop_all_video();
			}
		}, true );
	}


	/**
	 * Setup video.
	 *
	 * @since 0.2.0
	 *
	 * @param {object} video Video container.
	 */
	function setup_video( video ) {
		const provider = video.getAttribute( 'data-provider' );
		const id       = video.getAttribute( 'data-video' );

		video.addEventListener( 'click', ( event ) => {
			event.preventDefault();

			const iframe = create_iframe( provider, id );

			stop_all_video();
			pause_all_media();
			video.appendChild( iframe );
			video.classList.add( '-state_started' );
		} );

		video.classList.add( '-state_enabled' );
	}


	/**
	 * Generate iframe.
	 *
	 * @since 0.2.0
	 *
	 * @param  {string} provider Video provider.
	 * @param  {string} id       Video ID.
	 * @return {object}          Returned video HTML element.
	 */
	function create_iframe( provider, id ) {
		const iframe = document.createElement( 'iframe' );

		iframe.setAttribute( 'allowfullscreen', '' );
		iframe.setAttribute( 'allow', 'autoplay' );
		iframe.setAttribute( 'src', generate_url( provider, id ) );
		iframe.classList.add( 'sllv-video__iframe' );

		return iframe;
	}


	/**
	 * Generate URL.
	 *
	 * @since 0.2.0
	 *
	 * @param  {string} provider Video provider.
	 * @param  {string} id       Video ID.
	 * @return {string}          Returned video URL.
	 */
	function generate_url( provider, id ) {
		let url = '';

		if ( 'youtube' === provider ) {
			url = 'https://www.youtube.com/embed/' + id + '?rel=0&showinfo=0&autoplay=1';
		} else if ( 'vimeo' === provider ) {
			url = 'https://player.vimeo.com/video/' + id + '?autoplay=1';
		}

		return url;
	}


	/**
	 * Stop all video (YouTube, Vimeo).
	 *
	 * @since 0.9.0
	 */
	function stop_all_video() {
		const videos = document.querySelectorAll( '.sllv-video.-state_started' );

		// Remove all the iframe videos.
		if ( videos.length > 0 ) {
			videos.forEach( ( video ) => {
				const iframe = video.querySelector( '.sllv-video__iframe' );

				if ( iframe ) {
					iframe.remove();
				}

				video.classList.remove( '-state_started' );
			} );
		}
	}


	/**
	 * Pause all HTML media (video & audio).
	 *
	 * @since 0.9.0
	 */
	function pause_all_media() {
		const html_media = document.querySelectorAll( 'video, audio' );

		// Pause all the HTML video and audio.
		if ( html_media.length > 0 ) {
			html_media.forEach( ( media ) => {
				media.pause();
			} );
		}
	}

})();
