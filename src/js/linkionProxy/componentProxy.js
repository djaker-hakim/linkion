

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
            return (...args) => linkion.call(ref, method, args);
        }
    });
}