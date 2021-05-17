function init(){
    openMenu()
    opendropdown()
}

let openMenu = function () {
  
    let menu = document.querySelector('#js_icon')
    let container = document.querySelector('.site-content')
    function openMenu(e) {
        e.preventDefault()
        if (menu.classList.contains('is-opened')) {
            menu.classList.add('is-closed')
            document.body.classList.remove('with--sidebar')
            menu.classList.remove('is-opened')
            menu.classList.remove('active')
        } else {
            menu.classList.add('is-opened')
            menu.classList.add('active')
            document.body.classList.add('with--sidebar')
            menu.classList.remove('is-closed')
        }
    }

    menu.addEventListener('click', openMenu)

}

let opendropdown = function () {
    let opendropdown = document.querySelector('#js-dropdown')
    let list = document.querySelector('.topbar_menu_list_dropdown')
    if(opendropdown === null) {
        return
    }
    opendropdown.addEventListener('mouseover', function(e) {
        var dropdownContent = this.nextElementSibling;
        if (dropdownContent.classList.contains('show')) { 
            dropdownContent.classList.remove('show');    
        } else {
            dropdownContent.classList.add('show') 
        }
        document.body.addEventListener('click', function(e) {
            dropdownContent.classList.remove('show')
        })
    })
}

ready(init); 

function ready(fn) {
    if (document.attachEvent ? document.readyState === "complete" : document.readyState !== "loading"){
        fn();
    } else {
        document.addEventListener('DOMContentLoaded', fn);
    }
}