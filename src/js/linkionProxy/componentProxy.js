

export function componentProxy(component, linkion){

    return new Proxy(component, {
        get(target, method){
            // return real properties normally
            if (method in target) return target[method];
            if (method === 'then') return undefined; // avoid promise trap

            let ref = component.ref ? component.ref : component.componentName; 
            // reRendering the component
            if(method === 'render'){
                return (...args) => linkion.render(ref, ...args);
            }
            //uploading the file
            if(method === 'upload'){
                return (...args) => linkion.fileUpload(ref, ...args);
            }
            // adding a callback on component update
            if(method === 'onUpdate'){
                return (callback) => linkion.onUpdate(ref, callback);
            }
            // watch a prop for updates
            if(method === 'watch'){
                return (prop, callback) => linkion.onUpdate(ref, callback, prop);
            }
            return (...args) => linkion.call(ref, method, args);
        },

        set(_, prop, value){
            const props = {};
            props.ref = component.ref;
            props.componentName = component.componentName;
            props[prop] = value;
            linkion.updateComponent(props);
        }
    });
}