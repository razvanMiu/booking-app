<template>
    <div class="auth-form">
        <div class="auth-header">
            <h2 class="h2">Login</h2>
            <p class="h6">Please enter your email and password</p>
        </div>

        <div class="auth-body">
            <div class="form-group-item">
                <input v-model="email" type="email" name="email" class="form-control" placeholder="Email Address">
            </div>

            <div class="form-group-item">
                <input v-model="password" type="password" name="password" class="form-control" placeholder="Password">
            </div>


            <a class="h6">Forgot your password?</a>
            <a class="h6" @click="$emit('close')">Create a new account.</a>
        </div>

        <div class="auth-footer">
            <button class="btn btn-primary form-control" @click="login">Login</button>
        </div>
    </div>
</template>

<script>
    export default {
      data() {
          return {
            email: '',
            password: '',
          }
      },

      methods: {
        login: function() {
          this.axios
            .post('/login', {
                email: this.email,
                password: this.password,
            })
            .then(response => {
              let errorString = '';
              if(!response.data.authentication) {
                errorString = "Check your credentials";

                swal({
                    text: errorString,
                    icon: "error",
                    buttons: false
                });
              } else {
                window.location.href = "/";
              }

            })
            .catch(e => {
            });
        },
      },

    }
</script>
