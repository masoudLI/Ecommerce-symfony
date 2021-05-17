class BoiteModale {

    constructor(elements) {

        // les variables
        this.elements = elements
        this.doc = document.querySelector('#js-modale')
        this.dialog = null;
        const containers = Array.from(document.querySelectorAll('#js-block_container'))
        this.keyCodes = {
            tab: 9,
            enter: 13,
            escape: 27,  
        };
        let close = document.querySelector('.block_container_animate')

        // les evenements
        this.elements.forEach((element) => {
            this.dialog = document.getElementById(element.getAttribute('aria-controls'));
            element.addEventListener('click', (e) => {
                e.preventDefault();
                element.disabled = true
                element.textContent = ''
                var loader = document.createElement('span')
                loader.classList.add('section-loader-quart')
                loader.classList.add('section-loader')
                loader.style.display = 'block'
                loader.style.margin = 'auto'
                element.appendChild( loader );
                setTimeout(() => {
                    element.classList.remove('section-loader-quart')
                    element.classList.remove('section-loader')
                    element.disabled  = false;
                    element.textContent = 'En savoir plus...' 
                    element.style.removeProperty('width')
                    element.style.removeProperty('height')
                }, 1000);

             
            })
            
        })

        // open dialog
        containers.forEach(container => {
            container.addEventListener('keydown', (event) => {
                if (event.keyCode === this.keyCodes.enter) {
                    event.preventDefault();
                    this.open(this.dialog);
                }  
            });
        });
        
        this.dismissTriggers = document.querySelectorAll('[data-dismiss]');
        this.dismissTriggers.forEach((dismissTrigger) => {
            const dismissDialog = document.getElementById(dismissTrigger.dataset.dismiss);
            dismissTrigger.addEventListener('click', (e) => {
                e.preventDefault();
                close.classList.remove('isopened')
                this.close(dismissDialog)
            })
        })
    }


    open (dialog) {
        dialog.setAttribute('aria-hidden', false);
        this.doc.setAttribute('aria-hidden', true);
    }

    close (dialog) {
        dialog.setAttribute('aria-hidden', true);
        this.doc.setAttribute('aria-hidden', false);
    };
}

//initialize the boite Modale objects
let onReady = function () {
    new BoiteModale(document.querySelectorAll('[data-modale="modale-content"]'))
}
if (document.readyState !== 'loading') {
    onReady()
}
document.addEventListener('DOMContentLoaded', onReady)
  