<script setup>
import short from '@/router/axios';
import { ref } from 'vue';

const caption = ref();
const images = ref([]);
const token = localStorage.getItem('token');

// const post = async()=>{
//   const formdata = new FormData();
//   formdata.append("caption", caption.value);
//   for (let i = 0; i < images.value.files.length; i++) {
//   formdata.append("attachments[]", images.value.files[i]);
//   }
//   try {
//     const res = await short.post('v1/posts', formdata, {
//       headers:{
//         Authorization: `Bearer ${token}`
//       }
//     });
//     console.log(res.data);
//     window.location.href = '/home'
//   } catch (error) {
//     console.error(error);
    
//   }
// }

const post = async()=>{
    try {
        const formdata = new FormData();
        formdata.append('caption', caption.value);
        for (let i = 0; i < images.value.files.length; i++) {
            formdata.append('attachments[]', images.value.files[i]);
        }
        const res = await short.post('/whatever', )
    } catch (error) {
        console.error(error);
        
    }
}
</script>

<template>
<main class="mt-5">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between bg-transparent py-3">
                        <h5 class="mb-0">Create new post</h5>
                    </div>
                    <div class="card-body">
                        <form action="my-profile.html">
                            <div class="mb-2">
                                <label for="caption">Caption</label>
                                <textarea class="form-control" name="caption" id="caption" cols="30" rows="3" v-model="caption"></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="attachments">Image(s)</label>
                                <input type="file" class="form-control" id="attachments" name="attachments" ref="images" multiple/>
                            </div>

                            <button @click.prevent="post" class="btn btn-primary w-100">
                                Share
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>
</template>