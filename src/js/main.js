

// import "./linkion-script"
import Alpine from 'alpinejs'
import { Linkion } from './Linkion'
import { linkionProxy } from './linkionProxy/lnknProxy';

window.linkion = linkionProxy(new Linkion());

queueMicrotask(() => {
    window.linkion.start();
});
