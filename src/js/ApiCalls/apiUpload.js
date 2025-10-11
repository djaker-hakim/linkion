

export const apiUploadTrait = {
    progress: null,
    loading: false,

    fileUpload(name, prop, files){
        const xhr = new XMLHttpRequest;
        const formData = new FormData;

        formData.append('actions', 'upload');
        formData.append('props', JSON.stringify({
            componentName: name,
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
                this.emit('upload-progress', { progress: this.progress, componentName: name });
            }
        });

        xhr.onload = () => {
            if(xhr.status == 200){
                obj = JSON.parse(xhr.responseText);
                this.updateComponent(obj.props);
                console.log(this.get(obj.props.componentName));
            }else{
                this.displayError(xhr.responseText);
            }
            this.loading = false;
        }

        xhr.onerror = () => {
            console.error('error');
        }

        xhr.send(formData);

    }
}