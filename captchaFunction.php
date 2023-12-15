<script>
    grecaptcha.ready(function() {
        grecaptcha.execute('6LctIFYjAAAAAAzwY3EVOFvgUtQU5cVf1kV1d6HF', {action: 'submit'}).then(function(token) {
          // Add your logic to submit to your backend server here.
          var response = document.getElementById('token_generate');
          response.value = token;
      });
    });
</script>