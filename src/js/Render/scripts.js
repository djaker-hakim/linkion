// linkion scripts handler
export const scriptsTrait = {

    // init all linkion scripts
    initScripts(){
        this.getHead().append(...document.querySelectorAll(`script[lnkn-script]`));
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




}
