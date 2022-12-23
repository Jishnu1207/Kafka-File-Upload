<template>
    <div class="MappingDetails">
       
        <button v-on:click="submitForm()">mapping</button>
        <label>selected
        <ul>
            <li v-for="(item,index) in det" v-bind:key="index" >
              <strong>{{item}}</strong> 
              <select v-model="mapping[index]" >
                  <option value="">select</option>
                  <option value="first_name">first_name</option>
                  <option value="last_name">last_name</option>
                  <option value="city">city</option>
                  <option value="zip">zip</option>
                  <option value="email">email</option>
                  <option value="phone">phone</option>
                  <option value="company_name">company_name</option>
                </select>
                <br><br>
            </li>
        </ul>
        </label>
        <button v-on:click="SaveMapping()">save the mapping</button>
    </div>
</template>
<script>
  import axios from 'axios'
    export default {
       data(){
           return {
              id: this.$route.params.newid,
              det:null,
              mapping:[],
             selected:''
           }
       },
       methods:{
         submitForm()
         {
           let id= this.$route.params.newid;
           axios.post('http://127.0.0.1:8000/mapping',id,).then( (response)=> {
           if(!response.data){
             alert("error occured ");
           }
           else{
            // alert(response.data.lines);
            this.det=response.data.lines;
            console.log(this.det);
            
            
           }
         })
         .catch(function(){
           console.log("failure");
         })
       },
      SaveMapping(){
        let id= this.$route.params.newid;
        axios.post('http://127.0.0.1:8000/save',{'mapping':this.mapping,'id':id}
           )
        .then( (response)=> {
                    if(!response.data){
                        alert("error occured"); 
                    }else{
                        alert(response.data.message);
                        
                        
                    }
            })
            .catch(function(){
              console.log('FAILURE!!');
            });
      },
      }
  }
    
</script>
