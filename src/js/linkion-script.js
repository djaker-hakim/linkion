
import { apiToolsTrait } from './ApiCalls/apiTools';
import { apiLoadTrait } from './ApiCalls/apiLoad';
import { apiCallTrait } from './ApiCalls/apiCall';
import { apiUploadTrait } from './ApiCalls/apiUpload';

window.linkion = {
    
    // Traits
    ...apiToolsTrait,
    ...apiLoadTrait,
    ...apiCallTrait,
    ...apiUploadTrait,

    list: [],
    isReady: false,

    // event dispatcher
    emit(event, detail = {}) {
        document.dispatchEvent(new CustomEvent(event, { detail }));
    },

    // Main init method
    init(root = document) {
        this.emit('linkion:before-init');

        const elements = root.querySelectorAll('[lnkn-data]');
        
        elements.forEach(el => this.initComponent(el));

        this.isReady = true;
        this.emit('linkion:ready');
    },

    // Initialize one component
    initComponent(el) {
        const data = el.getAttribute('lnkn-data');
        if (!data) return;

        // Store it
        const component = JSON.parse(data);
        this.add(component);
        
        // Emit per-component event
        this.emit('linkion:component:init', { el, component});
    },

    // Initialize on DOM ready
    start(){
        // document.addEventListener('DOMContentLoaded', () => {
            this.emit('linkion:init');
            this.init();
        // });
    },

    add(props){
        if(this.has(props.componentName)) return;
        this.list.push(props);
    },

    get(name){
        return this.list.find((component) => component.componentName == name );                                
    },

    has(name){
        return !!this.get(name);
    },
}

queueMicrotask(() => {
    linkion.start();
})

