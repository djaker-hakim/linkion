// linkion scripts handler
export const assetsTrait = {

    assets: new Map(),


    // init all element linkion assets
    initAssets(root = document){
        this.setAssets(root, true);        
    }, 
    

    // handle element assets
    setAssets(root, active = false){
        const assets = root.querySelectorAll('[lnkn-asset]');
        for(let asset of assets){
            const key = asset.getAttribute('lnkn-asset')
            if(this.assets.has(key)){
                if(!this.assetExists(key, asset)){
                    active ? this.getHead().append(asset) :
                    [asset] = this.setScriptTags([asset], this.getHead(), false);
                    this.addAsset(key, asset);
                } 
            }else{
                active ? this.getHead().append(asset) :
                [asset] = this.setScriptTags([asset], this.getHead(), false); 
                this.addAsset(key, asset);
            }
        }
    },

    // check if component has asset
    assetExists(name, asset){
        if(this.assets.has(name)){
            const assets = this.assets.get(name);
            let status = false;
            for(const as of assets){
                status |= (as.innerText == asset.innerText);
            }
            return status;
        }
        return false;

    },

    addAsset(name, asset){
        if(this.assets.has(name)){
            this.assets.get(name).push(asset);
        }else{
            this.assets.set(name, [asset]);
        }
    },


}