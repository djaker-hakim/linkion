

export const eventsTrait = {

    
    //-----  external EVENTS   -----//

    //  send event from frontend to backend

    listeners: [],

    getListeners(event){
        return this.listeners.filter((listener) => {
            return listener.event == event;
        });
    },

    
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