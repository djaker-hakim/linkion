
export const coreTrait = {

    list: [],
    components: new Map(),
    isReady: false,

    // event dispatcher
    emit(event, detail = {}) {
        document.dispatchEvent(new CustomEvent(event, { detail: detail, bubbles: true }));
    },

    // Main init method
    init(root = document) {
        this.emit('linkion:before-init');

        this.initElement(root);
        
        this.isReady = true;
        this.emit('linkion:ready');
    },

    initElement(root){
        // get all linkion data elements
        const elements = root.querySelectorAll('[lnkn-data]');
        
        // init each component
        elements.forEach(el => this.initComponent(el));
    },

       
    // Initialize one component
    initComponent(el) {
        const data = el.getAttribute('lnkn-data');
        if (!data) return;

        // Store it
        const component = JSON.parse(data);
        this.add(component);
        this.addTemplate(component, el.outerHTML);
        
        // remove the lnkn-data attribute
        el.removeAttribute('lnkn-data')
        
        // Emit per-component event
        // this.emit('linkion:component:init', { el, component});
    },

    // Initialize on DOM ready
    start(){
        this.emit('linkion:init');
        this.init();
        this.initAssets();
        this.initScripts();
        this.setListeners();
    },

    add(props){
        if(!props._id){
            let ref = props.ref ? props.ref : props.componentName;
            this.has(ref) ? '' : this.list.push(props);
            return;        
        }
        !this.components.has(props._id) ? this.components.set(props._id, props): '';
        !this.getComponentByProp('_id',props._id) ? this.list.push(props): '';
        return;    
    },

    has(name){
        return (this.getComponentByProp( 'ref' ,name) || this.getComponentByProp( 'componentName' ,name));   
    },

    get(name){
        // get component by _id
        if(this.components.has(name)) return this.components.get(name);

        // get component by ref or componentName
        let comp = {};
        if((comp = this.getComponentByProp('ref', name)) ||
        (comp = this.getComponentByProp('componentName', name))) return comp;
        return null;                               
    },

    getComponentByProp(prop, value){
        return this.list.find((component) => component[prop] == value );
    },

    getComponentsByProp(prop, value){
        return this.list.filter((component) => component[prop] == value );
    },

    getCurrentComponent(el){
        if (!el || el.nodeType !== 1) return null

        const root = el.closest('[lnkn-id]');
        if(root) return this.getComponentByProp('_id' ,root.getAttribute('lnkn-id'));
        return null;

    },

    removeComponentByProp(prop, value){
        this.list = this.list.filter((component) => component[prop] != value );
    },

    cleanInactiveComponents(){
        const components = document.querySelectorAll('[lnkn-id]');
        // get active components
        let ids = [];
        for(let comp of components){
            ids.push(comp.getAttribute('lnkn-id'));
        }
        // get duplicated refs
        const refCount = {};
        this.list.forEach(comp => refCount[comp.ref] = (refCount[comp.ref] || 0) + 1);  
        const duplicateRefs = this.list.filter(comp => refCount[comp.ref] > 1);

        // remove inactive components

        // delete inactive components 
        for(let key of this.components.keys()){
            if(key && !ids.includes(key)) this.components.delete(key)        
        }
        // filter inactive components
        for(let comp of duplicateRefs){
            !this.components.has(comp._id) ? this.removeComponentByProp('_id', comp._id): ''; 
        }   
    },
}


