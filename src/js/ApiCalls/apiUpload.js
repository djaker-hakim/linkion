
// linkion upload method
export const apiUploadTrait = {
    progress: null,
    loading: false,

    fileUpload(name, prop, files){
        
        const xhr = new XMLHttpRequest;
        const formData = new FormData;
        const component = this.get(name);

        // throw error if component does not exist in the frontend
        if(!component) throw new Error('the linkion component ' + name + ' does not exist or is not loaded yet');

        formData.append('_token', this.getToken()); // important for 419 fix
        formData.append('action', 'upload');
        formData.append('props', JSON.stringify({
            componentName: component.componentName ,
            ref: component.ref,
            prop: prop 
        }));
        
        if(files instanceof FileList){
            for (let i = 0; i < files.length; i++) {
                formData.append(prop+'[]', files[i]);
            }
        }else{
            formData.append(prop, files);
        }


        xhr.open("POST", this.url);
        
        xhr.setRequestHeader("X-CSRF-TOKEN", this.getToken());

        xhr.onloadstart = () => {
            this.loading = true;
        }

        xhr.upload.addEventListener('progress', (e) => {
            if(e.lengthComputable) {
                this.progress = Math.round((e.loaded / e.total) * 100);
                this.emit('upload-progress', 
                    { 
                        progress: this.progress,
                        componentName: component.componentName,
                        ref: component.ref
                    });
            }
        });

        xhr.onload = () => {
            if(xhr.status == 200){
                obj = JSON.parse(xhr.responseText);
                this.updateComponent(obj.props);
            }else{
                this.displayError(xhr.responseText);
            }
            this.loading = false;
        }

        xhr.onerror = () => {
            // console.error('error'); // TODO 
        }

        xhr.send(formData);

    }
}