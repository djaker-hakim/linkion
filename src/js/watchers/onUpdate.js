

export const onUpdateTrait = {


    onUpdate(name, callback, prop = '_all_'){
        this.on('lnkn-updated', (detail) => {
                let { componentName, ref, props } = detail;

                let status;
                const component = linkion.get(name);
                if(!component) return; 

                component.ref ?
                status = component.ref == ref :
                status = component.componentName == componentName;
                if(status && (props.includes(prop) || prop == '_all_')) callback(detail)                
            });

    }

}