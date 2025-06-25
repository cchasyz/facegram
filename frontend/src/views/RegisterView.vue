<script setup>
import short from '@/router/axios';
import { ref } from 'vue';

const fn = ref();
const usn = ref();
const pass = ref();
const bio = ref();
const is_private = ref(false);
const token = localStorage.getItem('token');
const username = localStorage.getItem('username');
const errormessage = ref();
if(token){
  window.location.href = `/profile/` + username;
}

const register = async()=>{
    try {
        const res = await short.post('v1/auth/register',{
            full_name: fn.value,
            username: usn.value,
            password: pass.value,
            bio: bio.value,
            is_private: is_private.value
        });
        console.log(res.data);
        localStorage.setItem('token', res.data.token);
        localStorage.setItem('username', usn.value);
        window.location.href = `/profile/` + usn.value;
    } catch (error) {
        console.error(error);
        errormessage.value = error.response.data.message
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
                        <h5 class="mb-0">Register</h5>
                    </div>
                    <div class="card-body">
                        <form action="homepage.html">
                            <div class="mb-2">
                                <label for="full_name">Full Name</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" v-model="fn"/>
                            </div>

                            <div class="mb-2">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" id="username" name="username" v-model="usn"/>
                            </div>

                            <div class="mb-3">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" v-model="pass"/>
                            </div>

                            <div class="mb-3">
                                <label for="bio">Bio</label>
                                <textarea name="bio" id="bio" cols="30" rows="3" class="form-control" v-model="bio"></textarea>
                            </div>

                            <div class="mb-3 d-flex align-items-center gap-2">
                                <input type="checkbox" id="is_private" name="is_private" v-model="is_private"/>
                                <label for="is_private">Private Account</label>
                            </div>

                            <div v-if="errormessage">{{ errormessage }}</div>

                            <button @click.prevent="register" class="btn btn-primary w-100">
                                Register
                            </button>
                        </form>
                    </div>
                </div>

                <div class="text-center mt-4">
                    Already have an account? <a href="/login">Login</a>
                </div>

            </div>
        </div>
    </div>
</main>
</template>