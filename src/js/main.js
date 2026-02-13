

// import "./linkion-script"
import { Linkion } from './Linkion'
import { linkionProxy } from './linkionProxy/lnknProxy';

window.linkion = linkionProxy(new Linkion());
 
queueMicrotask(() => {
    window.linkion.start();
});

