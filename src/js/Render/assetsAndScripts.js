
// linkion scripts handler
export const assetsAndScriptsTrait = {

    assets: {},
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

    // check if the asset exist
    assetsHas(name){
        return Object.keys(this.assets).includes(name);
    },

    // return the saved asset
    assetsGet(name){
        return this.assets[name];
    },

    // set the new asset
    assetsSet(name, value){
        this.assets[name] = value;
    },

    // set array of assets
    assetsAdd(name, asset){
        this.assets[name].push(asset);
    },
    // check if component has asset
    componentHasAsset(name, asset){
        if(this.assetsHas(name)){
            const assets = this.assetsGet(name);
            let status = false;
            for(let compAsset of assets){
                status |= (compAsset.innerText == asset.innerText);
            }
            return status;
        }
        return false;
    },
    
    // init all element linkion assets
    initAssets(root = document){
        this.setAssets(root, true);        
    }, 

    // init all linkion scripts
    initScripts(){
        this.getHead().append(...document.querySelectorAll(`script[lnkn-script]`));
    },

    // handle element assets
    setAssets(root, active = false){
        const assets = root.querySelectorAll('[lnkn-asset]');
        for(let asset of assets){
            let key = asset.getAttribute('lnkn-asset')
            if(this.assetsHas(key)){
                if(this.componentHasAsset(key, asset)){
                    asset.remove();
                }else{
                    active ? this.getHead().append(asset) :
                    [asset] = this.setScriptTags([asset], this.getHead(), false);
                    this.assetsAdd(key, asset);
                } 
            }else{
                active ? this.getHead().append(asset) :
                [asset] = this.setScriptTags([asset], this.getHead(), false); 
                this.assetsSet(key, [asset]);
            }
        }
    },

    // set component scripts
    setScripts(id, template){
        const oldScripts = document.querySelectorAll(`script[lnkn-script=${id}]`);
        const scripts = template.querySelectorAll(`script[lnkn-script=${id}]`);

        for(let script of oldScripts){
            script.remove();
        }
        this.setScriptTags(scripts, this.getHead());
        return template;
    },

    // clear all old scripts
    cleanScripts(){
        const scripts = document.querySelectorAll('script[lnkn-script]');
        for(let script of scripts){
            this.components.has(script.getAttribute('lnkn-script')) ? '':
            script.remove();
        }  
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