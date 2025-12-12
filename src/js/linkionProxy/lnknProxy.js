
const toDotted = (name) => name.replace(/([a-z])([A-Z])/g, '$1.$2').toLowerCase();

export function linkionProxy(linkion){
    return new Proxy(linkion, {
        get(target, component){
        // return real properties normally
        if (component in target) return target[component];
        if (component === 'then') return undefined; // avoid promise trap

        // for existing components
        if(target.get(component)){
            return new Proxy(target.get(component), {
                get(target2, method){
                    // return real properties normally
                    if (method in target2) return target2[method];
                    if (method === 'then') return undefined; // avoid promise trap
                    // reRendering the component
                    if(method === 'render'){
                        return (...args) => target.render(component, ...args);
                    } 
                    return (...args) => target.call(component, method, args);
                }
            });
        }

        return new Proxy({}, {
            get(_, method){
                // rendering the component
                if(method === 'render'){
                    return (...args) => target.render(toDotted(component), ...args);
                } 
            }
        });
    }
    });
} 