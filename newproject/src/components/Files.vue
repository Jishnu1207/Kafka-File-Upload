<template>
    <div class = "overflow-auto"><br>
        <h2 style = "text-align : center;"> FILE DETAILS </h2><br>
        <b-table id = "my-table" striped hover 
            :items = "items"
            :fields = "fields"
            @sort-changed = "sort"
        >
            <!-- <template #cell(SNo) = "row">
                {{ row.index+1 }}
            </template>   -->
            <template #cell(uploaded_at) = "row">
                {{ row.value?.date }}
            </template> 
            <template #cell(action) = "row">
                <b-button @click = "$router.push({ path : '/contacts/'+row.item.id }) "> View </b-button>
            </template>  
        </b-table>
    
        <b-pagination
            @input = "callPagination"
            v-model = "currentPage"
            :total-rows = "rows"
            :per-page = 3
            aria-controls = "my-table"
        >
        </b-pagination>
    </div>
</template>
  
<script>
    import axios from 'axios'
    export default 
    {
        name : "FileDetails",
        data() 
        {
            return {
                currentPage : 1,
                sortBy : "uploaded_at",
                sortOrder : 'DESC',
                fields : [
                  // { key : 'SNo' },
                  { key : 'new_name',sortable : true },
                  { key : 'record_count' },
                  { key : 'uploaded_at',sortable : true },
                  { key : 'action' }
                ],
                items : [],
                rows : 0
            }
        },

        methods : 
        {
            fileList(pageNo,sortField,sortOrder)
            { 
                axios.post('http://127.0.0.1:8000/files',{ 'pageNo' : pageNo, 'sortField' : sortField, 'sortOrder' : sortOrder}) 
                    .then( response=> 
                    {
                        this.items = response.data.listData;
                        this.rows = response.data.total;
                    })
                    .catch( function(error) 
                    {
                        console.log(error); 
                    })
                },

            callPagination()
            {
                console.log("call pagination");
                this.index = (this.currentPage-1) * 3 ;
                this.fileList(this.currentPage , this.sortBy , this.sortOrder);
            },

            sort(data) 
            {
                this.currentPage = 1;
                this.sortBy = data.sortBy;
                if(data.sortDesc == false)

                    this.sortOrder = "ASC";
                else
                    this.sortOrder = "DESC";

                this.fileList(this.currentPage , this.sortBy , this.sortOrder);
            }
        },

        created() 
        {
            this.fileList(1 , this.sortBy , this.sortOrder);
        },
    }
</script>
