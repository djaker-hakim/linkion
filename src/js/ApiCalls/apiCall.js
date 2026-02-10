
// Linkion Call Method
export const apiCallTrait = {
    async call(name, method, args = []){
        const component = this.get(name);
        const train = { 
            props: component,
            method: {
                name: method,
                args: args
            },
            action: {}
        };
        try{
            const data = await this.fetch(train);
            this.handleEvents(data.events);
            this.updateComponent(data.props);
            if(!component.componentCached || data.template){
                this.renderTemplate(component, data.template);
            } 
            return data.result; 
        } catch(e) {
            console.error(e);
            return e;
        }
    },    
}