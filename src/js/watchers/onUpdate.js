
// linkion OnUpdate method
export const onUpdateTrait = {

    onUpdate(name, callback, prop = '_all_'){
        this.on('lnkn-updated', (detail) => {
            const { componentName, ref, props } = detail;

            let status;
            const component = linkion.get(name);
            if(!component) return; 

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
    }

}