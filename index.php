<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar Sesión</title>
  <!-- FontAwesome para los íconos -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <style>
    * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    font-weight: bold;
    background-color: #f3f4f6;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 90vh;
}

header {
    margin-bottom: 2rem;
    text-align: center;
}
main {
    width: 100%;
    display: flex;
    justify-content: center;
}
    .login-container {
        padding: 5rem 3rem 5rem 3rem; 
    border-radius: 10px;
    box-shadow: 0 1px 8px rgba(0, 0, 0, 0.1);
    text-align: center;
    width: 400px; }
    .form-group {
      position: relative;
      margin-bottom: 1rem;
    }
    input[type="text"],
    input[type="password"] {
      width: 100%;
      padding: 10px;
      font-size: 1rem;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    .icon-container {
      position: absolute;
      top: 50%;
      right: 10px;
      transform: translateY(-50%);
      cursor: pointer;
      color: #888;
    }
    .icon-container:hover {
      color: #333;
    }
    button[type="submit"] {
      width: 100%;
      padding: 10px;
      font-size: 1rem;
      background-color: #4f6df7;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
  </style>
</head>
<body>
  <div class="login-container">
  <header>
        <img src="vistas/logo.png" class="img-fluid rounded-top" alt="50px" width="50px" height="50px" />
      </header>
    <form action="php/login.php" method="POST">
      <div class="form-group">
        <input type="text" id="userUser" name="userUser" placeholder="Usuario" required>
      </div>
      <div class="form-group">
        <input type="password" id="userPassword" name="userPassword" placeholder="Contraseña" required>
        <span class="icon-container" id="togglePassword">
          <i class="fas fa-eye-slash"></i>
        </span>
      </div>
      <button type="submit">Iniciar Sesión</button>
    </form>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const togglePassword = document.getElementById('togglePassword');
      const passwordInput = document.getElementById('userPassword');

      togglePassword.addEventListener('click', function() {
        const currentType = passwordInput.getAttribute('type');
        const newType = currentType === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', newType);

        // Alterna el icono
        const icon = this.querySelector('i');
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
      });
    });
  </script>
</body>
</html>
