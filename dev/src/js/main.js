(function() {

	sllv_find_videos();


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
	 * Setup video
	 *
	 * @since 0.2.0
	 *
	 * @param  {object} video Video container
	 */
	function sllv_setup_video( video ) {
		let link   = video.querySelector( '.sllv-video__link' );
		let media  = video.querySelector( '.sllv-video__media' );
		let button = video.querySelector( '.sllv-video__button' );

		let provider = video.getAttribute( 'data-provider' );
		let id       = video.getAttribute( 'data-video' );

		video.addEventListener( 'click', () => {
			let iframe = sllv_create_iframe( provider, id );

			link.remove();
			button.remove();
			video.appendChild( iframe );
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
		iframe.classList.add( 'sllv-video__media' );

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

})();
