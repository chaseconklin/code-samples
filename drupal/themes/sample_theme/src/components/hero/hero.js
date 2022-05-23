{
  let vid = document.getElementsByTagName('video');
  if (vid.length > 0) {
    vid[0].addEventListener('ended', function () {
      vid[0].load();
      vid[0].play();
    });
  }
}
