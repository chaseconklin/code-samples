{
  let overflowSubButtons = document.querySelectorAll(".overflow__expand");

  let overflowSubToggleMenu = function () {
    this.classList.toggle("-open");
    this.classList.toggle("-close");
    this.nextElementSibling.classList.toggle("-opened");
  }

  if (overflowSubButtons) {
    overflowSubButtons.forEach(function (btn) {
      btn.addEventListener('click', overflowSubToggleMenu);
    });
  }
}
