
export const renderTrait = {

    templates: new Map(),

    addTemplate(component, template){
        if(!this.hasTemplate(component.componentName)){
            this.templates.set(component.componentName, template);
        } 
    },
    
    getTemplate(name){
        return this.templates.get(name);
    },
    
    hasTemplate(name){
        return this.templates.has(name);
    },

    // linkion render method
    async render(name, args = {}, el){
        const component = this.get(name);
        // template is cached
        if(component && this.hasTemplate(name)){
            return Promise.resolve(
                this.renderTemplate(component, this.getTemplate(name), el)
            );
        }
        // fetching the template
        const train = { 
            props: component ? component : {componentName: name, ...args},
            method: {name: 'render'},
            action: 'render'
        };
        try{
            const data = await this.fetch(train);
            component ? this.updateComponent(data.props) : this.add(data.props);
            this.renderTemplate(this.get(name), data.template, el);
            return data.result; 
        } catch(e) {
            console.error(e);
            return e;
        }
    },

    // handle component templates
    renderTemplate(component, template, el = null){
        
        // new Rendering
        if(el) return this.renderComponent(component, template, el);

        // re-Rendering
        if(document.querySelector(`[lnkn-id=${component._id}]`)
        ) return this.reRenderComponent(component, template);

        // caching
        return this.addTemplate(component, template);

    },


    // render a linkion component
    renderComponent(component, template, el){
        // rendering the component to the dom
        let oldTemplate = el;
        let newTemplate = oldTemplate.cloneNode(true);
        newTemplate.innerHTML = template;
        
        // register for nested component
        this.init(newTemplate);
        
        //scan for new components
        let comps = newTemplate.querySelectorAll('[lnkn-id]');
        let ids = [];
        for(let comp of comps){
            ids.push(comp.getAttribute('lnkn-id'));
        }
        ids = ids.filter((id) => id != component._id);

        
        //setup assets
        this.setAssets(newTemplate);
        
        // MOUNT THE COMPONENTS
        oldTemplate.outerHTML = newTemplate.innerHTML;
        
        // setup scripts
        for(let id of ids){
            this.setScripts(id, newTemplate); 
        }
        this.setScripts(component._id, newTemplate);

        // cleaning linkion and dom from old component and scripts
        this.cleanInactiveComponents();
        this.cleanScripts();

    },

    // rerender a linkion component
    reRenderComponent(component, template){

        let oldTemplate = document.querySelector(`[lnkn-id=${component._id}]`);
        let newTemplate = document.createElement('div');
        newTemplate.innerHTML = template;

        // register for nested component
        this.init(newTemplate);
        
        // scan for new nested component
        let comps = newTemplate.querySelectorAll('[lnkn-id]');
        let ids = [];
        for(let comp of comps){
            ids.push(comp.getAttribute('lnkn-id'));
        }
        // nested component ids
        ids = ids.filter((id) => id != component._id);

        // setup assets
        this.setAssets(newTemplate, true);

        // mout the component
        oldTemplate.outerHTML = newTemplate.innerHTML;

        // setup scripts
        if(!ids.length == 0){
            for(let id of ids){
                this.setScripts(id, newTemplate); 
            }
        } 
        this.setScripts(component._id, newTemplate);

        // cleaning linkion and dom from old component and scripts 
        this.cleanInactiveComponents();
        this.cleanScripts();
        return;
    },
}