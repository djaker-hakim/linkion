
import { apiToolsTrait } from './ApiCalls/apiTools';
import { apiLoadTrait } from './ApiCalls/apiLoad';
import { apiCallTrait } from './ApiCalls/apiCall';
import { apiUploadTrait } from './ApiCalls/apiUpload';
import { renderTrait } from './Render/renderTools';
import { assetsAndScriptsTrait } from './Render/assetsAndScripts';

window.linkion = {
    
    // Traits
    ...apiToolsTrait,
    ...apiLoadTrait,
    ...apiCallTrait,
    ...apiUploadTrait,
    ...renderTrait,
    ...assetsAndScriptsTrait,

    list: [],
    components: new Map(),
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
        this.addTemplate(component, el.outerHTML);
        
        // Emit per-component event
        this.emit('linkion:component:init', { el, component});
    },

    // Initialize on DOM ready
    start(){
        this.emit('linkion:init');
        this.init();
        this.initAssets();
        this.initScripts();
        
    },

    add(props){
        if(!props._id){
            this.has(props.componentName) ?
            '' :
            this.list.push(props);
            return;
        }
        if(!this.getComponentByProp('_id',props._id)){
            this.components.set(props._id, props);
            this.list.push(props);
        }
        return;
    },

    
    get(name){
        if(this.components.has(name)) return this.components.get(name);
        let comp = {};
        if((comp = this.getComponentByProp('componentName', name)) ||
        (comp = this.getComponentByProp('id', name))) return comp;
        return null;                               
    },

    getComponentByProp(prop, value){
        return this.list.find((component) => component[prop] == value );
    },

    getComponentsByProp(prop, value){
        return this.list.filter((component) => component[prop] == value );
    },

    removeComponentByProp(prop, value){
        this.list = this.list.filter((component) => component[prop] != value );
    },

    has(name){
        return this.components.has(name) && 
        (this.getComponentByProp( 'componentName' ,name) || this.getComponentByProp( 'id' ,name));   
    },

    cleanInactiveComponents(){
        const components = document.querySelectorAll('[lnkn-id]');
        let ids = [];
        for(let comp of components){
            ids.push(comp.getAttribute('lnkn-id'));
        }
        for(let key of this.components.keys()){
            if(key && !ids.includes(key)){
                this.components.delete(key);
                this.removeComponentByProp('_id', key);
            }     
        }       
    },
}

queueMicrotask(() => {
    linkion.start();
})

