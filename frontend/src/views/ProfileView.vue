<script setup>
import { useRoute } from 'vue-router';
import short from '@/router/axios';
import { onMounted, ref } from 'vue';

const route = useRoute();
const username = route.params.username;
const token = localStorage.getItem('token');
const user = ref({});
const followers = ref();
const following = ref();

const getUser = async()=>{
  try {
    const res = await short.get(`v1/users/${username}`, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    });
    console.log(res.data.data);
    
    user.value = res.data.data;
  } catch (error) {
    console.error(error);
    
  }
}
const getFollower = async()=>{
  try {
    const res = await short.get(`v1/users/${username}/followers`, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    });
    console.log(res.data.followers);
    followers.value = res.data.followers
  } catch (error) {
    console.error(error);
    
  }
}
const getFollowing = async()=>{
  try {
    const res = await short.get(`v1/users/${username}/following`, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    });
    console.log(res.data.following);
    following.value = res.data.following
  } catch (error) {
    console.error(error);
    
  }
}
const follow = async()=>{
  try {
    const res = await short.post(`v1/users/${username}/follow`,{}, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    });
    console.log(res.data);
    // getUser();
  } catch (error) {
    console.error(error);
    
  } finally {
    getUser();
  }
}
const unfollow = async()=>{
  try {
    const res = await short.delete(`v1/users/${username}/unfollow`,{
      headers: {
        Authorization: `Bearer ${token}`
      }
    });
    console.log(res.data);
    
  } catch (error) {
    console.error(error);
    
  } finally {
    getUser();
  }
}
const deletepost = async(id)=>{
  try {
    const res = await short.delete(`v1/posts/${id}`,{
      headers: {
        Authorization: `Bearer ${token}`
      }
    });
    console.log(res.data);
    getUser();
  } catch (error) {
    console.error(error);
    
  }
}
onMounted(()=>{
  getUser();
  getFollower();
  getFollowing();
})
</script>

<template>
  <main class="mt-5">
    <div class="container py-5">
        <div class="px-5 py-4 bg-light mb-4 d-flex align-items-center justify-content-between">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h5 class="mb-0">{{ user.full_name }}</h5>
                    <span>@{{user.username}}</span>
                </div>
                <small class="mb-0 text-muted">
                    {{ user.bio }}
                </small>
            </div>
            <div>
                <a v-if="user.following_status === 'not-following' && user.is_your_accout === 'true'" href="/post" class="btn btn-primary w-100 mb-2">
                    + Create new post
                </a>

                <a v-if="user.following_status === 'not-following' && user.is_your_accout === 'false'" @click.prevent="follow" href="" class="btn btn-primary w-100 mb-2">
                    Follow
                </a>

                <a v-if="user.following_status === 'requested'" @click.prevent="unfollow" href="" class="btn btn-secondary w-100 mb-2">
                    Requested
                </a>

                <a v-if="user.following_status === 'following'" @click.prevent="unfollow" href="" class="btn btn-secondary w-100 mb-2">
                    Following
                </a>

                <div class="d-flex gap-3">
                    <div>
                        <div class="profile-label"><b>{{ user.posts_count }}</b> posts</div>
                    </div>
                    <div class="profile-dropdown">
                        <div class="profile-label"><b>{{ user.followers_count }}</b> followers</div>
                        <div class="profile-list">
                            <div class="card">
                                <div class="card-body">
                                    <div class="profile-user" v-for="f in followers" :key="f.id">
                                        <a :href="'/profile/' + f.username">@{{ f.username }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>  
                    </div>
                    <div class="profile-dropdown">
                        <div class="profile-label"><b>{{ user.following_count }}</b> following</div>
                        <div class="profile-list">
                            <div class="card">
                                <div class="card-body">
                                    <div class="profile-user" v-for="f in following" :key="f.id">
                                        <a :href="'/profile/' + f.username">@{{f.username}}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center" v-if="user.is_private == 0 || user.is_private == 1 && user.following_status === 'following'">
            <div class="col-md-4"  v-for="post in user.posts" :key="post.id">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="card-images mb-2">
                            <img v-for="att in post.attachments" :key="att.id" :src="'http://127.0.0.1:8000/storage/' + att.storage_path" alt="image" class="w-100"/>
                        </div>
                        <p class="mb-0 text-muted">{{ post.caption }}</p>
                        <button @click.prevent="deletepost(post.id)">delete post</button>
                    </div>
                </div>
            </div>
          </div>
          <div v-if="user.is_private === 1 && user.following_status === 'not-following' && !user.is_your_accout || user.is_private === 1 && user.following_status === 'requested' && !user.is_your_accout " class="card py-4">
            <div class="card-body text-center">
                &#128274; This account is private
            </div>
          </div>
    </div>
</main>
</template>