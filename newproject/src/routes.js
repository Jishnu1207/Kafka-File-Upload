import mapping from './components/MappingDetails.vue'
import fileUpload from './components/fileUpload.vue'
import Files from './components/Files.vue'
import Contacts from './components/Contacts.vue'

export default[
    {path: '/',component:fileUpload},
    {path: '/mapping/:newid',component:mapping},
    {path: '/filelist',component:Files},
    {path: '/contacts/:file_id',component:Contacts}

]