/**
 * Authors: Valerii Bohdanov
 */

jQuery(function() {
  sllv_findVideos();
});

function sllv_findVideos() {
  let videos = document.querySelectorAll('.sllv-video');

  for (let i = 0; i < videos.length; i++) {
    sllv_setupVideo(videos[i]);
  }
}

function sllv_setupVideo(video) {
  let link   = video.querySelector('.sllv-video__link');
  let media  = video.querySelector('.sllv-video__media');
  let button = video.querySelector('.sllv-video__button');

  let provider = video.getAttribute('data-provider');
  let id       = video.getAttribute('data-video');

  video.addEventListener('click', () => {
    let iframe = sllv_createIframe(provider, id);

    link.remove();
    button.remove();
    video.appendChild(iframe);
  });

  link.removeAttribute('href');
  video.classList.add('-state_enabled');
}

function sllv_createIframe(provider, id) {
  let iframe = document.createElement('iframe');

  iframe.setAttribute('allowfullscreen', '');
  iframe.setAttribute('allow', 'autoplay');
  iframe.setAttribute('src', sllv_generateURL(provider, id));
  iframe.classList.add('sllv-video__media');

  return iframe;
}

function sllv_generateURL(provider, id) {
  let url = '';

  if (provider == 'youtube') {
    url = 'https://www.youtube.com/embed/' + id + '?rel=0&showinfo=0&autoplay=1';
  } else if (provider == 'vimeo') {
    url = 'https://player.vimeo.com/video/' + id + '?autoplay=1';
  }

  return url;
}
