
// Linkion Load method 
export const apiLoadTrait = {
    async load(name, args = {}){
        if(this.has(name)) return Promise.resolve(this.get(name));

        const train = { 
                props: {componentName: name, ...args},
                method: {},
                action:{}
            }
        try{
            const data = await this.fetch(train); // parse JSON response
            this.add(data.props);
            return this.get(name); 
        } catch(e) {
            return e
        }
    },
}