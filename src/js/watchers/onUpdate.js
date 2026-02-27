
// linkion OnUpdate method
export const onUpdateTrait = {

    watchers: new Map(),

    onUpdate(name, callback, prop = '_all_'){

        if(this.has(name)){
            
            const component = this.get(name);
            let id = `${name}:${prop}:${this.hash(callback.toString())}`;

            if(this.watchers.has(id)) this.offId(this.watchers.get(id));
                 
            let eventId = this.on(name, 'lnkn-updated', (detail) => {
                const { componentName, ref, props } = detail;

                let status;
                
                component.ref ?
                status = component.ref == ref :
                status = component.componentName == componentName;
                
                // watching for all props
                if(status && prop == '_all_'){
                    callback(props);
                    return;
                } 
                // watching for spicific prop
                if(status && Object.keys(props).includes(prop)) 
                {
                    callback(props[prop]);
                    return;
                }
            });
            this.watchers.set(id, eventId);
        }
    },

    hash(str){
        let h = 0
        for (let i = 0; i < str.length; i++) {
            h = Math.imul(31, h) + str.charCodeAt(i) | 0
        }
        return h.toString(36) // short alphanumeric
    },

    removeWatcher(name){
        for(const [key, value] of this.watchers){
            let watcherName = key.split(":")[0];
            watcherName == name ? this.watchers.delete(key) : '';
        }
    }
}