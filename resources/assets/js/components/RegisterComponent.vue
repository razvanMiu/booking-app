<template>
    <div class="auth-form">
        <div class="auth-header">
            <h2 class="h2">Register</h2>
            <p class="h6">Please provide your credentials</p>
        </div>

        <div class="auth-body">
            <div class="form-group-item">
                <input v-model="name" type="text" name="name" class="form-control" placeholder="Name">
            </div>

            <div class="form-group-item">
                <input v-model="email" type="email" name="email" class="form-control" placeholder="Email Address">
            </div>

            <div class="form-group-item">
                <input v-model="password" type="password" name="password" class="form-control" placeholder="Password">
            </div>

            <div class="form-group-item">
                <input v-model="password_confirmation" type="password" name="password_confirmation" class="form-control" placeholder="Password Confirmation">
            </div>

            <a class="h6" @click="$emit('close')">Have an account? Login here</a>
        </div>

        <div class="auth-footer">
            <button class="btn btn-primary form-control" @click="register">Register</button>
        </div>
    </div>
</template>

<script>
    export default {
      data() {
        return {
          name: '',
          email: '',
          password: '',
          password_confirmation: '',
        }
      },

      methods: {
        register: function() {
          this.axios
            .post('/register', {
              name: this.name,
              email: this.email,
              password: this.password,
              password_confirmation: this.password_confirmation
            })
            .then(response => {
              window.location.href = "/";
            })
            .catch(e => {
                let errorString = '';
                for(let error in e.response.data.errors) {
                  errorString += e.response.data.errors[error][0] + '\n';
                }

                this.swal({
                    text: errorString,
                    icon: "error",
                    buttons: false
                });
            })
        }
      }
    }
</script>
