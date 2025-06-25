<script setup>
import short from './router/axios';

const token = localStorage.getItem('token');
const username = localStorage.getItem('username');

const logout = async()=>{
  try {
    const res = await short.post('v1/auth/logout',{}, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    });
    console.log(res.data);
    localStorage.removeItem('token');
    localStorage.removeItem('username');
    window.location.href = '/'
  } catch (error) {
    console.error(error);
    
  }
}
</script>

<template>
  <div>
    <nav v-if="!token" class="navbar navbar-expand-lg bg-body-tertiary fixed-top">
        <div class="container">
            <a class="navbar-brand m-auto" href="">Facegram</a>
        </div>
    </nav>

    <nav v-else class="navbar navbar-expand-lg bg-body-tertiary fixed-top">
    <div class="container">
        <a class="navbar-brand" href="/home">Facegram</a>
        <div class="navbar-nav">
            <a class="nav-link" :href="'/profile/' + username">@{{username}}</a>
            <a @click.prevent="logout" class="nav-link" href="">Logout</a>
        </div>
    </div>
    </nav>

    <RouterView />
  </div>
</template>

<style scoped>

</style>
