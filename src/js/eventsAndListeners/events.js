

export const eventsTrait = {

     handleEvents(events){
        events.forEach(event => {
            this.emit(event.name, event.detail);
        });
    }
    
}