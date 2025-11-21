<?php
// Configurar título da página
$title = 'Esqueceu sua senha';

// Incluir head
include 'includes/head.php';

// Incluir navegação pública
$navType = 'public';
include 'includes/nav.php';
?>

  <!-- Main Content -->
  <div class="min-h-[calc(100vh-20rem)] flex items-center justify-center py-8 px-4">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
      <h1 class="text-2xl font-bold text-purple-600 text-center mb-1">Esqueceu sua senha?</h1>
      <p class="text-gray-600 text-center text-sm mb-4">Não se preocupe! Digite seu email e enviaremos instruções para redefinir sua senha.</p>
      
      <form id="forgotPasswordForm" class="space-y-3">
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
          <input 
            type="email" 
            id="email" 
            name="email" 
            placeholder="Coloque seu endereço de Email" 
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent"
          >
        </div>

        <div id="messageContainer" class="hidden p-3 rounded-lg"></div>

        <button 
          type="submit" 
          class="w-full bg-purple-600 text-white py-2.5 rounded-lg font-semibold hover:bg-purple-700 transition"
        >
          Enviar instruções
        </button>
      </form>

      <div class="mt-4 text-center">
        <a href="login.php" class="text-purple-600 hover:text-purple-700 font-medium">
          Voltar para o login
        </a>
      </div>
    </div>
  </div>

  <script>
    document.getElementById('forgotPasswordForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const email = document.getElementById('email').value;
      const messageContainer = document.getElementById('messageContainer');
      
      // Check if email exists in localStorage (simulating backend check)
      const users = JSON.parse(localStorage.getItem('users') || '[]');
      const companies = JSON.parse(localStorage.getItem('companies') || '[]');
      
      const userExists = users.some(user => user.email === email) || 
                        companies.some(company => company.email === email);
      
      if (userExists) {
        // Show success message
        messageContainer.className = 'p-3 rounded-lg bg-green-100 text-green-700';
        messageContainer.textContent = 'Instruções de redefinição de senha foram enviadas para seu email!';
        messageContainer.classList.remove('hidden');
        
        // Simulate sending reset email (in real app, this would be a backend call)
        console.log('[v0] Password reset email would be sent to:', email);
        
        // Redirect to login after 3 seconds
        setTimeout(() => {
          window.location.href = 'login.php';
        }, 3000);
      } else {
        // Show error message
        messageContainer.className = 'p-3 rounded-lg bg-red-100 text-red-700';
        messageContainer.textContent = 'Email não encontrado. Verifique o endereço digitado.';
        messageContainer.classList.remove('hidden');
      }
    });
  </script>
<?php
// Incluir footer padrão
include 'includes/footer.php';
?>
