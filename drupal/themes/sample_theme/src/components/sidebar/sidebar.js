{
  let profileImg = document.querySelectorAll('.sidebar__section.-full img');
  let sidebar = document.querySelectorAll('.sidebar');

  if (profileImg.length !== 0) {
    profileImg[0].classList.add('sidebar__image');
  }

  if (sidebar.length !== 0) {
    sidebar.forEach(
      function (sidebarBlock) {
        sections = sidebarBlock.querySelectorAll('.sidebar__section');
        if (sections.length > 0) {
          lastSection = sections[sections.length - 1];
          lastSection.classList.add('-last');
        }
      }
    )
  }
}
