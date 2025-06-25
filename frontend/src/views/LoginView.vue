<script setup>
import short from '@/router/axios';
import { ref } from 'vue';

const usn = ref();
const password = ref();
const token = localStorage.getItem('token');
const username = localStorage.getItem('username');
const errormessage = ref();
if(token){
  window.location.href = `/profile/` + username;
}

const login = async()=>{
  try {
    const res = await short.post('/v1/auth/login', {
      username: usn.value,
      password: password.value
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
                        <h5 class="mb-0">Login</h5>
                    </div>
                    <div class="card-body">
                        <form action="homepage.html">
                            <div class="mb-2">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" id="username" name="username" v-model="usn" />
                            </div>

                            <div class="mb-3">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" v-model="password" />
                            </div>

                            <div v-if="errormessage">{{ errormessage }}</div>

                            <button @click.prevent="login" class="btn btn-primary w-100">
                                Login
                            </button>
                        </form>
                    </div>
                </div>

                <div class="text-center mt-4">
                    Don't have account? <a href="/">Register</a>
                </div>

            </div>
        </div>
    </div>
</main>
</template>