function sidebarToggle() {
    const sidebar = document.getElementById('sidebar');
    const main = document.getElementById('main');
    const nav = document.getElementById('nav');
    const isEnable = sidebar.getAttribute('toggled') === 'true';
    if(isEnable) {
        sidebar.style.display = 'none';
        main.style.marginLeft = '0';
        main.style.width = '100vw';
        nav.style.marginLeft = '0';
        nav.style.width = '100vw';
        sidebar.setAttribute('toggled', 'false');
    } else {
        sidebar.style.display = 'flex';
        main.style.marginLeft = 'var(--sidebar-width)';
        main.style.width = 'var(--main-width)';
        nav.style.marginLeft = 'var(--sidebar-width)';
        nav.style.width = 'var(--main-width)';
        sidebar.setAttribute('toggled', 'true');
    }
}

function toggleByOpacity(id) {
    console.log(id);
    const div = document.getElementById(id);
    const isEnable = div.classList.contains('opacity_1');
    if(isEnable) {
        div.classList.remove('opacity_1');
        div.classList.add('opacity_0');
    } else {
        div.classList.add('opacity_1');
        div.classList.remove('opacity_0');
    }
}

function toggleActiveClass(obj) {
    if(typeof obj  == "string") {
        obj = document.getElementById(id);
    }
    const isEnable = obj.classList.contains('active');
    if(isEnable) {
        obj.classList.remove('active');
    } else {
        obj.classList.add('active');
    }
}

function sidebarDropdown(dropdownButton) {
    const isEnable = dropdownButton.classList.contains('sidebar_dropdown_enabled');
    const dropdownContent = dropdownButton.nextElementSibling;
    if(isEnable) {
        dropdownButton.classList.remove('sidebar_dropdown_enabled');
        dropdownContent.style.display = 'none';
    } else {
        dropdownButton.classList.add('sidebar_dropdown_enabled');
        dropdownContent.style.display = 'flex';
    }
}

function goToLink(link) {
    window.location.href = link;
}

