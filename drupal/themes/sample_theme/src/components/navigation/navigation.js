{
  // Menus
  let primaryNavMobile = document.querySelector("nav.-mobile");
  let subNavMobile = document.querySelector(".navigation__menu.-mobile");

  let subMenus = document.querySelectorAll(".navigation__submenu");

  if (primaryNavMobile) {

    // Buttons
    let subNavToggleButtons = primaryNavMobile.querySelectorAll(".navigation__expand.-submenu");
    let primaryNavToggleButton = primaryNavMobile.querySelector(".navigation__expand");

    let primaryToggleMenu = function() {
      primaryNavToggleButton.classList.toggle("-open");
      primaryNavToggleButton.classList.toggle("-close");
      subNavMobile.classList.toggle("-open");
    }

    let subToggleMenu = function() {
      this.classList.toggle("-open");
      this.nextElementSibling.classList.toggle("-open");
    }

    // Bind events
    if (subNavToggleButtons && primaryNavToggleButton) {
      primaryNavToggleButton.addEventListener('click', primaryToggleMenu);
      subNavToggleButtons.forEach(function (btn) {
        btn.addEventListener('click', subToggleMenu);
      });
    }
  }

  let toggleHeaderMenu = function(evt) {
    if (evt.type == 'mouseenter' && !(this.lastElementChild.classList.contains('-visible'))) {
      this.lastElementChild.classList.add('-visible');
    }
    else if (evt.type == 'mouseleave' && this.lastElementChild.classList.contains('-visible')) {
      this.lastElementChild.classList.remove('-visible');
    }
    else if (evt.type == 'focusin' && (!(this.contains(evt.relatedTarget))) && !(this.lastElementChild.classList.contains('-visible'))) {
      this.lastElementChild.classList.add('-visible');
    }
    else if (evt.type == 'focusout' && (!(this.contains(evt.relatedTarget))) && this.lastElementChild.classList.contains('-visible')) {
      this.lastElementChild.classList.remove('-visible');
    }
  }

  if (subMenus.length > 0) {
    subMenus.forEach(function (menu) {
      menu.parentNode.addEventListener('mouseenter', toggleHeaderMenu);
      menu.parentNode.addEventListener('mouseleave', toggleHeaderMenu);
      menu.parentNode.addEventListener('focusin', toggleHeaderMenu);
      menu.parentNode.addEventListener('focusout', toggleHeaderMenu);
    });
  }

}
