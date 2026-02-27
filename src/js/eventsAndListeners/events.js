
// linkion event handler
export const eventsTrait = {

    //-----  internal EVENTS   -----//

    triggers: {},

    // internal event regeister
    on(ref, event, callback){
        let id = null
        if(this.has(ref)){
            if(!this.triggers[ref]) this.triggers[ref] = {};
            if(!this.triggers[ref][event]) this.triggers[ref][event] = new Map();
            id = crypto.randomUUID();
            this.triggers[ref][event].set(id, callback);
        }
        return id;
    },

    // internal event unregister
    off(ref, event = null, id = null){
        if(this.has(ref) && !event){
            delete this.triggers[ref];
            return null;
        } 
        if(this.has(ref) && this.triggers[ref][event]){
           if(id){
                this.triggers[ref][event].has(id) ? 
                this.triggers[ref][event].delete(id) :
                '';
           }else {
                this.triggers[ref][event] = null;
           }
        }
    },

    offId(id){
        for(let ref of Object.keys(this.triggers)){
            for(let event of Object.keys(this.triggers[ref])){
                this.triggers[ref][event].has(id) ? 
                this.triggers[ref][event].delete(id) :
                '';
            }
        }
    },

    // internal event dispatcher
    trigger(event, detail = {}) {
        for(let ref of Object.keys(this.triggers)){
            if(!this.triggers[ref][event]) continue;
            this.triggers[ref][event].forEach(cb => cb(detail));
        }
    },

    
    //-----  external EVENTS   -----//

    //  send event from frontend to backend

    listeners: [],

    getListeners(event){
        return this.listeners.filter((listener) => {
            return listener.event == event;
        });
    },

    // get backend listeners
    async setListeners(){
        const train = { 
            action: 'getListeners'
        };

        try{
            const data = await this.fetch(train);
            for(listener of data){
                this.listeners.push(listener);
            }         
        } catch(e) {
            console.error(e);
        }
    },


    // external event dispatcher
    dispatch(event, detail = {}){

        for(let listener of this.getListeners(event)){
            let components = this.getComponentsByProp('componentName', listener.componentName);
            for(let component of components){
                this.call(component.componentName, listener.method, [detail]);
            }    
        }
        
    },

    // send event from backend to frontend
    handleEvents(events){
        events.forEach(event => {
            this.emit(event.name, event.detail);
        });
    }

}