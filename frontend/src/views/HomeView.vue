<script setup>
import short from '@/router/axios';
import { ref, onMounted } from 'vue';

const posts = ref([]);
const users = ref([]);
const loader = ref(null);
const loading = ref(false);
const page = ref(0);
const userRequests = ref([]);
const token = localStorage.getItem('token');
if(!token){
  window.location.href = '/'
}

// let observerr;

// observerr = new IntersectionObserver((entrys) => {
//   if(entrys[0].isIntersecting){
//     getPosts();
//   }
// })

// if(loader.value){
//   observerr.observe(loader.value);
// } 

const getUser = async()=>{
  try {
    const res = await short.get('v1/users', {
      headers: {
        Authorization: `Bearer ${token}`
      }
    })
    console.log(res.data.users);
    users.value = res.data.users
  } catch (error) {
    console.error(error);
    
  }
}

const getPosts = async()=>{
  if(loading.value)return
  try {
    loading.value = true;
    const res = await short.get('v1/posts', {
      params: {
        page: page.value
      },
      headers: {
        Authorization: `Bearer ${token}`
      }
    });
    console.log(res.data.data);
    posts.value.push(...res.data.data);
    page.value++;
    loading.value = false;
  } catch (error) {
    console.error(error);
    
  }
}

const getUserRequest = async()=>{
  try {
    const res = await short.get('v1/users/request', {
      headers: {
        Authorization: `Bearer ${token}`
      }
    });
    console.log(res.data);
    userRequests.value = res.data.data;
  } catch (error) {
    console.error(error);
    
  }
}

const accept = async(usn)=>{
  try {
    const res = await short.put(`v1/users/${usn}/accept`, {}, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    });
    console.log(res.data);
    
  } catch (error) {
    console.error(error);
    
  }
}

let observer;

onMounted(()=>{
  getPosts();
  getUser();
  getUserRequest();

  observer = new IntersectionObserver((entries) => {
    if (entries[0].isIntersecting) {
      getPosts();
    }
  });

  if (loader.value) {
    observer.observe(loader.value);
  }
});
</script>

<template>
  <main class="mt-5">
    <div class="container py-5">
        <div class="row justify-content-between">
            <div class="col-md-8">
                <h5 class="mb-3">News Feed</h5>
                <div class="card mb-4" v-for="post in posts" :key="post.id">

                    <div class="card-header d-flex align-items-center justify-content-between bg-transparent py-3">
                        <h6 class="mb-0">{{ post.user.full_name }}</h6>
                        <small class="text-muted">5 days ago</small>
                    </div>

                    <div class="card-body">
                        <div class="card-images mb-2">
                            <img v-for="att in post.attachments" :key="att.id" :src="'http://127.0.0.1:8000/storage/' + att.storage_path" alt="image" class="w-100"/>
                        </div>
                        <p class="mb-0 text-muted"><b><a :href="'/profile/' + post.user.username">{{ post.user.username }}</a></b>  {{ post.caption }}</p>
                    </div>  
                  </div>
                  <div ref="loader">
                    loading . . .
                  </div>
            </div>
            <div class="col-md-4">
                <div class="request-follow mb-4" v-if="userRequests.length > 0">
                    <h6 class="mb-3">Follow Requests</h6>
                    <div class="request-follow-list">
                        <div class="card mb-2" v-for="us in userRequests" :key="us.id">
                            <div class="card-body d-flex align-items-center justify-content-between p-2">
                                <a href="user-profile-private.html">@{{us.username}}</a>
                                <a @click.prevent="accept(us.username)" href="" class="btn btn-primary btn-sm">
                                    Confirm
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="explore-people">
                    <h6 class="mb-3">Explore People</h6>
                    <div class="explore-people-list">
                        <div class="card mb-2" v-for="user in users" :key="user.id">
                            <div class="card-body p-2">
                                <a :href="'/profile/' + user.username">@{{ user.username }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
</template>
