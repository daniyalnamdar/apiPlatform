<template>
  <form v-on:submit.prevent="handleSubmit">
    <div v-if="error" className="alert alert-danger">
      {{ error }}
    </div>
    <div className="form-group">
      <label htmlFor="exampleInputEmail1">Email address</label>
      <input type="email" v-model="email" className="form-control" id="exampleInputEmail1"
             aria-describedby="emailHelp" placeholder="Enter email">
    </div>
    <div className="form-group">
      <label htmlFor="exampleInputPassword1">Password</label>
      <input type="password" v-model="password" className="form-control"
             id="exampleInputPassword1" placeholder="Password">
    </div>
    <div className="form-check">
      <input type="checkbox" className="form-check-input" id="exampleCheck1">
      <label className="form-check-label" htmlFor="exampleCheck1">I like cheese</label>
    </div>
    <button type="submit" className="btn btn-primary" v-bind:class="{ disabled: isLoading }">Log in</button>
  </form>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      email: '',
      password: '',
      error: '',
      isLoading: false
    }
  },
  props: ['user'],
  methods: {
    handleSubmit() {
      this.isLoading = true;
      this.error = '';

      axios
          .post('/login', {
            email: this.email,
            password: this.password
          })
          .then(response => {
            console.log(response.data);

            this.$emit('user-authenticated', response.headers.location);
            this.email = '';
            this.password = '';
          }).catch(error => {
        if (error.response.data.error) {
          this.error = error.response.data.error;
        } else {
          this.error = 'Unknown error';
        }
      }).finally(() => {
        this.isLoading = false;
      })
    },
  },
}
</script>

<style scoped lang="scss">
</style>
