

export const apiCallTrait = {
    async call(component, method, args = []){

        const train = { 
            props: this.get(component),
            methods: {
                method: method,
                args: args
            },
            actions: {}
        };
        try{
            const data = await this.fetch(train);
            this.updateComponent(data.props);
            return data.result; 
        } catch(e) {
            return e
        }
    },
}