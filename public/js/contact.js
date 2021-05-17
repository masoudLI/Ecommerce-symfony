
/**
 * initialisation des functions
 */
function init(){
    deactivateTooltips()
    setFormListeners()    
}

/**
 * Fonction de désactivation de l'affichage des "tooltips"
**/
function deactivateTooltips() {
    var tooltips = Array.from(document.querySelectorAll('.invalid'));
    console.log(tooltips)
    for (var i = 0; i < tooltips.length; i++) {
        tooltips[i].style.display = 'none';
    }

}
/**
 * La fonction ci-dessous permet de récupérer la "tooltip" qui correspond à notre div
 * @param {HTMLElement} div 
*/

function getTooltip(div) {
    // je vais cherche la tooltip dans la div
    let tooltip = div.querySelector('.invalid')
    return tooltip
}

/**
 * check the fiele name
 */
function verifName() {
    let div_name = document.querySelector('#js-name')
    let name = div_name.querySelector('.js-input-name')
    let tooltipStyleName = getTooltip(div_name).style;
    if(name.value.length < 2 || name.value.length > 25) {
        name.style.border = "1px solid #cd5353";
        tooltipStyleName.display = "block";
        return false;
    } else {
        name.classList.add('valid');
        name.style.border = "";
        tooltipStyleName.display = "none";
        return true;
    }
}

/**
 * check the field mail
 */
function verifEmail() {
    let div_email = document.querySelector('#js-email')
    let email = div_email.querySelector('.js-input-email')
    let tooltipStyleEmail = getTooltip(div_email).style;
    var regex = /^[a-zA-Z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$/;
    if(!regex.test(email.value)) {
        email.style.border = "1px solid #cd5353";
        tooltipStyleEmail.display = "block";
        return false;
    } else {
        email.classList.add('valid');
        email.style.border = "";
        tooltipStyleEmail.display = "none";
        return true;
    }
}

/**
 * check the field objet
 */
function verifSujet() {
    let div_sujet = document.querySelector('#js-sujet')
    let sujet = div_sujet.querySelector('.js-input-sujet')
    let tooltipStyleSujet = getTooltip(div_sujet).style;

    if(sujet.value === '') {
        sujet.style.border = "1px solid #cd5353";
        tooltipStyleSujet.display = "block";
        return false;
    } else {
        sujet.classList.add('valid');
        sujet.style.border = "";
        tooltipStyleSujet.display = "none";
        return true;
    }
}

/**
 * check the field message
 */
function verifMessage() {
    let div_message = document.querySelector('#js-message')
    let message = div_message.querySelector('.js-input-message')
    let tooltipStyleMessage = getTooltip(div_message).style;
  
    if(message.value === "") {
        message.style.border = "1px solid #cd5353";
        tooltipStyleMessage.display = "block";
        return false;
    } else {
        message.classList.add('valid');
        message.style.border = "";
        tooltipStyleMessage.display = "none";
        return true;
    }
}

function setFormListeners() {
    
    let myFrom = document.querySelector('#js-contact')
    if(myFrom == null) {
        return
    }
    myFrom.addEventListener('submit', function(e) {
        e.preventDefault()
        verifName()
        verifSujet()
        verifEmail()
        verifMessage()
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


