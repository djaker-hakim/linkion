import { componentProxy } from "./componentProxy";
const toDotted = (name) => name.replace(/([a-z])([A-Z])/g, '$1.$2').toLowerCase();

// linkion proxy
export function linkionProxy(linkion){
    return new Proxy(linkion, {
        get(target, component){
        // return real properties normally
        if (component in target) return target[component];
        if (component === 'then') return undefined; // avoid promise trap

        // for existing components
        if(target.get(component)){
            return componentProxy(target.get(component), target);
        }

        // for non existing components
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