{
  const tables = document.querySelectorAll('.content__table');

  // Adds some basic mobile responsive fallbacks
  tables.forEach(function(table) {
    const wrapper = document.createElement('div');
    wrapper.classList.add('wrapper', '-tableScroll');

    table.parentNode.insertBefore(wrapper, table);
    wrapper.appendChild(table);
  });
}
