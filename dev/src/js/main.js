(function() {

	sllv_find_videos();
	sllv_media_playing();


	/**
	 * Find videos
	 *
	 * @since 0.2.0
	 */
	function sllv_find_videos() {
		let videos = document.querySelectorAll( '.sllv-video' );

		for ( let i = 0; i < videos.length; i++ ) {
			sllv_setup_video( videos[i] );
		}
	}


	/**
	 * Do some actions if HTML media starts playing
	 *
	 * @since X.X.X
	 */
	function sllv_media_playing() {
		const html_media = document.querySelectorAll( 'video, audio' );
		html_media.forEach( ( media ) => {
			media.addEventListener( 'play', () => {
				sllv_stop_all_video();
			} );
		} );
	}


	/**
	 * Setup video
	 *
	 * @since 0.2.0
	 *
	 * @param {object} video Video container
	 */
	function sllv_setup_video( video ) {
		let link     = video.querySelector( '.sllv-video__link' );
		let provider = video.getAttribute( 'data-provider' );
		let id       = video.getAttribute( 'data-video' );

		video.addEventListener( 'click', () => {
			let iframe = sllv_create_iframe( provider, id );

			sllv_stop_all_video();
			sllv_pause_all_media();
			video.appendChild( iframe );
			video.classList.add( '-state_started' );
		} );

		link.removeAttribute( 'href' );
		video.classList.add( '-state_enabled' );
	}


	/**
	 * Generate iframe
	 *
	 * @since 0.2.0
	 *
	 * @param  {string} provider Video provider
	 * @param  {string} id       Video ID
	 * @return {string}          Returned video HTML
	 */
	function sllv_create_iframe( provider, id ) {
		let iframe = document.createElement( 'iframe' );

		iframe.setAttribute( 'allowfullscreen', '' );
		iframe.setAttribute( 'allow', 'autoplay' );
		iframe.setAttribute( 'src', sllv_generate_url( provider, id ) );
		iframe.classList.add( 'sllv-video__iframe' );

		return iframe;
	}

	/**
	 * Generate URL
	 *
	 * @since 0.2.0
	 *
	 * @param  {string} provider Video provider
	 * @param  {string} id       Video ID
	 * @return {string}          Returned video URL
	 */
	function sllv_generate_url( provider, id ) {
		let url = '';

		if ( provider == 'youtube' ) {
			url = 'https://www.youtube.com/embed/' + id + '?rel=0&showinfo=0&autoplay=1';
		} else if (provider == 'vimeo') {
			url = 'https://player.vimeo.com/video/' + id + '?autoplay=1';
		}

		return url;
	}


	/**
	 * Stop all video (YouTube, Vimeo)
	 *
	 * @since X.X.X
	 */
	function sllv_stop_all_video() {
		const videos = document.querySelectorAll( '.sllv-video.-state_started' );

		// Remove all the iframe videos
		if ( videos.length > 0 ) {
			videos.forEach( ( video ) => {
				let iframe = video.querySelector( '.sllv-video__iframe' );

				iframe.remove();
				video.classList.remove( '-state_started' );
			} );
		}
	}


	/**
	 * Pause all HTML media (video & audio)
	 *
	 * @since X.X.X
	 */
	function sllv_pause_all_media() {
		const html_media = document.querySelectorAll( 'video, audio' );

		// Pause all the HTML video and audio
		if ( html_media.length > 0 ) {
			html_media.forEach( ( media ) => {
				media.pause();
			} );
		}
	}

})();
