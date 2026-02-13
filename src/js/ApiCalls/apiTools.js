
// linkion Helper tools
export const apiToolsTrait = {
    token: null,
    url: "/linkion/connection",

    // returns a csrf token
    getToken(){
        !this.token ? 
        this.token = document.querySelector('[data-token]').getAttribute('data-token') : 
        '';
        return this.token;
    },

    // linkion fetch helper
    async fetch(train){
        this.token = document.querySelector('[data-token]').getAttribute('data-token');
        try{
            const response = await fetch(this.url, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": this.token,
                    'Accept': 'application/json',
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(train)
            });
            
            if (!response.ok) {
                const data = await response.text();
                return this.displayError(data); // display the error
            }
            return await response.json(); // parse JSON response

        } catch(e) {
            return e
        }
    },
    
    // linkion component update method
    updateComponent(props){
        let ref = props.ref ? props.ref : props.componentName;
        const component = this.get(ref);
        const updatedProps = {};
        Object.keys(props).forEach((key) => {
            if(!(component[key] === props[key])){
                component[key] = props[key];
                updatedProps[key] = component[key];
            }
        });
        if(!(updatedProps.length === 0)){
            this.trigger('lnkn-updated', {
                componentName: component.componentName,
                ref: component.ref,
                props: updatedProps
            });
        }
    },

    // display backend errors  
    displayError(error){
        const container = document.createElement('section');
        const div = document.createElement('div');
        container.style.width = "100vw"
        container.style.height = "100vh"
        container.style.padding = "8px"
        container.style.display = "flex"
        container.style.justifyContent = "center"
        container.style.position = "absolute"
        container.style.top = 0
        container.style.zIndex = 999

        div.style.width = "95%"
        div.style.backgroundColor = "white"
        div.innerHTML=error;

        container.append(div);
        document.querySelector('body').append(container);
    }

}