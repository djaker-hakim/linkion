

// Linkion imports
import { Linkion } from './Linkion'
import { linkionProxy } from './linkionProxy/lnknProxy';
import { componentProxy } from './linkionProxy/componentProxy';

// Alpine imports
import Alpine from 'alpinejs'
import intersect from '@alpinejs/intersect';
import collapse from '@alpinejs/collapse';
import mask from '@alpinejs/mask';
import persist from '@alpinejs/persist';
import focus from '@alpinejs/focus';
import morph from '@alpinejs/morph';
import anchor from '@alpinejs/anchor';
import sort from '@alpinejs/sort';
import resize from '@alpinejs/resize';

window.linkion = linkionProxy(new Linkion());

Alpine.plugin(intersect)
Alpine.plugin(collapse)
Alpine.plugin(anchor)
Alpine.plugin(mask)
Alpine.plugin(persist)
Alpine.plugin(focus)
Alpine.plugin(sort)
Alpine.plugin(morph)
Alpine.plugin(resize)

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
