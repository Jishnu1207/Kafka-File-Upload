<template>
    <div class="container">
        <div class="large-12 medium-12 small-12 cell">
            <h1> file Upload using PHP </h1>
            <label>File
              <input type="file" id="file" accept=".csv,.txt,.xlx" name="file" ref="file"  v-on:change="onChangeFileUpload()"/>
            </label>
              <button v-on:click="submitForm()">Upload</button>
        </div>
    </div>
</template>
<script>
import axios from 'axios'
  export default {
    data(){
      return {
        file: ''
      }
    },
    methods: {
      submitForm(){
            let formData = new FormData();
            formData.append('file', this.file);
            axios.post('http://127.0.0.1:8000/file',
                formData,
                {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
              }
            ).then( (response)=> {
                    if(!response.data){
                        alert("error occured"); 
                    }
                    else if(response.data.message==0){
                      alert("please select file");
                    }
                    else{
                        alert(response.data.message);
                        alert(response.data.id);
                        //alert(response.data.upload);
                        let NewId=response.data.id;
                        console.log(NewId);
                        this.$router.push({path: '/mapping/'+NewId});                        
                    }
            })
            .catch(function(){
              console.log('FAILURE!!');
            });
      },
      onChangeFileUpload(){
        this.file = this.$refs.file.files[0];
      }
    }
  }
</script>