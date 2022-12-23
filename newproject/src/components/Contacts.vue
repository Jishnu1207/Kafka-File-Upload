<template>
    <div class = "overflow-auto"><br>
        <h2 style = "text-align : center;"> CONTACTS </h2><br>
        <div class = "search-bar">
            <b-form-input 
                style = "text-align: left;"
                v-model = "text" 
                type = "text"
                placeholder = "Enter email" 
            >
            </b-form-input>
            <span class = "search-icon">
                  <i class = "fas fa-search"></i>
            </span>
            <b-button @click = "search()"> Search </b-button>
        </div>
        <br/>
        
        <b-table id = "my-table" striped hover 
            :items = "items"
            :fields = "fields"
            @sort-changed = "sort"
        >
            <!-- <template #cell(SNo) = "row">
                {{row.index+1}}
            </template>   -->
            
            <template #cell(modified_date) = "row">
                {{row.item.modified_date.date}}
            </template> 
            
            <template #cell(action) = "row">
                <b-button @click = "info(row.item, row.index, $event.target)">View</b-button>
            </template>  
        
        </b-table>
      
        <b-pagination
            @input = "callpagination"
            v-model = "currentPage"
            :total-rows = "rows"
            aria-controls = "my-table"
        ></b-pagination>

        <b-modal :id = "infoModal.id" :title = "infoModal.title" ok-only >
            <pre>{{ infoModal.content }}</pre>
        </b-modal>
    </div>
</template>

<script>
    import axios from 'axios'
    export default 
    {
        name : "ContactDetails",
        data() 
        {
            return {
                currentPage : 1,
                sortBy : "modified_date",
                sortOrder : 'DESC',
                text : '',
                fields : [
                  // { key : 'SNo' },
                  { key : 'email' , sortable : true },
                  { key : 'modified_date' , sortable : true },
                  { key : 'action'}
                ],
                items : [],
                rows : 0,
                infoModal : {
                    id : 'info-modal',
                    title : '',
                    content : ''
                }
            }
        },


        methods: 
        {
            fileList ( pageNo , sortField , sortOrder , searchText )
            {
                let fileId = this.$route.params.file_id;
                axios.post('https://127.0.0.1:8001/contacts',{'fileId' : fileId, 'pageNo' : pageNo, 'sortField' : sortField, 'sortOrder' : sortOrder, 'searchText' : searchText})
                    .then(response=> 
                    {
                        if(response.data.total != 0)
                        {
                            this.items = response.data.listData;
                            this.rows = response.data.total;
                        }
                        else
                            console.log("no data!");

                    })
                    .catch(function( error ) 
                    {
                        console.log(error); 
                    })
                },

            callpagination()
            {
                this.fileList(this.currentPage , this.sortBy  ,this.sortOrder , this.text);
            },

            sort(data) 
            {
                this.currentPage = 1;
                this.sortBy = data.sortBy;
                if(data.sortDesc == false)
                        this.sortOrder = "ASC";
                else
                        this.sortOrder = "DESC";
                this.fileList(this.currentPage , this.sortBy , this.sortOrder , this.text);
            },
                
            search() 
            {
                let email = this.text;
                this.fileList(this.currentPage , this.sortBy , this.sortOrder , email);
            },

            info(item , index , button) 
            {
                this.infoModal.title = `Row index: ${index}`
                this.infoModal.content = item, null, 2
                this.$root.$emit('bv::show::modal', this.infoModal.id, button)
            },
                
        },

        created() 
        {
            this.fileList(1 , this.sortBy , this.sortOrder , this.text);
        },
    }
    
</script>
/*  
@input = "search()"

:sort-by = "sortBy"
:sort-desc = "sortOrder  =  =  'desc'"

@hide = "resetInfoModal"

resetInfoModal () 
            {
                this.infoModal.title = ''
                this.infoModal.content = ''
            }
*/