import { coreTrait } from './core-script';
import { apiToolsTrait } from './ApiCalls/apiTools';
import { apiLoadTrait } from './ApiCalls/apiLoad';
import { apiCallTrait } from './ApiCalls/apiCall';
import { apiUploadTrait } from './ApiCalls/apiUpload';
import { renderTrait } from './Render/renderTools';
import { assetsAndScriptsTrait } from './Render/assetsAndScripts';
import { eventsTrait } from './eventsAndListeners/events';

export class Linkion {
    static traits = [
        coreTrait,
        apiCallTrait,
        apiToolsTrait,
        apiLoadTrait,
        apiUploadTrait,
        renderTrait,
        assetsAndScriptsTrait,
        eventsTrait
    ];

    static register(...traits){
        this.constructor.traits.push(...traits);
    }
    
    constructor(){
        Object.assign(this, ...this.constructor.traits);
    }
}