

// import "./linkion-script"
import Alpine from 'alpinejs'
import { Linkion } from './Linkion'
import { linkionProxy } from './linkionProxy/lnknProxy';
import { componentProxy } from './linkionProxy/componentProxy';

window.linkion = linkionProxy(new Linkion());
window.Alpine = Alpine;
 
queueMicrotask(() => {
    window.linkion.start();
    window.Alpine.start();
});

document.addEventListener('alpine:init', () => {
    Alpine.magic('lnkn', (el) => {
        let comp = {};
        if(comp = window.linkion.getCurrentComponent(el)){
            return componentProxy(comp, window.linkion);
        }
    })
    
});
