
// linkion event handler
export const eventsTrait = {

    //-----  internal EVENTS   -----//

    triggers: {},

    // internal event regeister
    on(event, callback){
        (this.triggers[event] ??= []).push(callback);
    },

    // internal event unregister
    off(event){
        this.triggers[event] = []
    },

    // internal event dispatcher
    trigger(event, detail = {}) {
        (this.triggers[event] || []).forEach(cb => cb(detail));
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
            actions: 'getListeners'
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