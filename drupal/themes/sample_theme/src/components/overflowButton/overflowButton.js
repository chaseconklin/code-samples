{
  let overflowButtons = document.querySelectorAll("button.overflowButton__button");
  overflowMenu = document.querySelector("section.overflow");

  function toggleMenu() {

    /**
     * To allow for display: none to be used with an animation, we must use a
     * delay to allow for the animation to finalize before applying display: none
     */
    if (overflowMenu.classList.contains("-opened")) {
      overflowMenu.classList.toggle("-opened");
      setTimeout(function () {
        overflowMenu.classList.toggle("-block");
      }, 700);
    } else {
      overflowMenu.classList.toggle("-block");
      // Force reflow now that element is back in the DOM
      overflowMenu.offsetWidth;
      overflowMenu.classList.toggle("-opened");
    }

  }

  // If overflow buttons are hit
  if (overflowButtons && overflowMenu) {
    overflowButtons.forEach(function(overflowButton) {
      overflowButton.addEventListener("click", toggleMenu);
    });
  }

  // Handle ESC key to dismiss menu
  window.addEventListener('keyup', function(e) {
    if (e.key === 'Escape' && overflowMenu.classList.contains("-opened")) {
      toggleMenu();
    }
  });
}
