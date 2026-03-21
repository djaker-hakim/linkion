

export const scriptToolsTrait = {

    head: null,
    body: null,
    // get the DOM Head element
    getHead(){
        if(!this.head) this.head = document.querySelector('head');
        return this.head; 
    },
    // get the DOM Body element
    getBody(){
        if(!this.body) this.body = document.querySelector('body');
        return this.body; 
    },

    // reload a script
    setScriptTags(scripts, el, att){
        let newScripts = [];
        for(let script of scripts){
            const newScript = document.createElement('script');
            this.copyElement(script, newScript, att);
            script.remove();
            el.append(newScript);
            newScripts.push(newScript);
        }
        return newScripts;
    },

    copyElement(source, target, script = true){
        // Loop through all attributes of the source element
        for (let attr of source.attributes) {
            target.setAttribute(attr.name, attr.value);
        }
        if(script){
            target.textContent = `(() => {${source.textContent}})();`;
        }else{
            target.textContent = source.textContent;
        }    
    },

}