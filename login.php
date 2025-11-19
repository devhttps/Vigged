<?php
// Iniciar sessão para exibir mensagens
require_once 'config/auth.php';
startSecureSession();

// Configurar título da página
$title = 'Login';

// Incluir head
include 'includes/head.php';

// Incluir navegação pública
$navType = 'public';
include 'includes/nav.php';
?>

    <!-- Login/Cadastro Form -->
    <div class="min-h-screen flex items-center justify-center py-12 px-4">
        <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md">
            <!-- Tabs -->
            <div class="flex border-b border-gray-200 mb-6">
                <button 
                    id="loginTab"
                    onclick="switchTab('login')"
                    class="flex-1 py-3 px-4 text-center font-medium text-purple-600 border-b-2 border-purple-600 transition"
                >
                    Login
                </button>
                <button 
                    id="cadastroTab"
                    onclick="switchTab('cadastro')"
                    class="flex-1 py-3 px-4 text-center font-medium text-gray-500 hover:text-purple-600 transition"
                >
                    Cadastrar-se
                </button>
            </div>

            <!-- Mensagens -->
            <?php
            // Exibir mensagens de erro
            if (isset($_SESSION['login_errors']) && !empty($_SESSION['login_errors'])) {
                echo '<div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">';
                foreach ($_SESSION['login_errors'] as $error) {
                    echo '<p>' . htmlspecialchars($error) . '</p>';
                }
                echo '</div>';
                unset($_SESSION['login_errors']);
            }
            
            // Exibir mensagem de sucesso no cadastro
            if (isset($_GET['cadastro']) && $_GET['cadastro'] === 'success') {
                echo '<div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">';
                echo '<p>Cadastro realizado com sucesso! Você já pode fazer login.</p>';
                echo '</div>';
            }
            ?>
            
            <!-- Login Form -->
            <div id="loginFormContainer">
                <h1 class="text-2xl font-bold text-purple-600 text-center mb-6">Bem vindo de volta!</h1>
                <form id="loginForm" action="processar_login.php" method="POST" class="space-y-6">
                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email"
                        placeholder="Coloque seu endereço de Email"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent outline-none transition"
                    >
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Senha</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password"
                        placeholder="Digite sua senha"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent outline-none transition"
                    >
                </div>

                <!-- Forgot Password Link -->
                <div class="text-left">
                    <a href="esqueceu-senha.php" class="text-purple-600 hover:text-purple-700 text-sm font-medium">Esqueceu sua senha?</a>
                </div>

                <!-- Login Button -->
                <button 
                    type="submit"
                    class="w-full bg-purple-600 text-white py-3 rounded-lg font-medium hover:bg-purple-700 transition"
                >
                    Entrar
                </button>

                <!-- Divider -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">ou</span>
                    </div>
                </div>

                <!-- Google Login Button -->
                <button 
                    type="button"
                    onclick="loginWithGoogle()"
                    class="w-full border-2 border-red-500 text-red-500 py-3 rounded-full font-medium hover:bg-red-50 transition flex items-center justify-center space-x-2"
                >
                    <svg class="w-5 h-5" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="currentColor" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="currentColor" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="currentColor" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    <span>Entrar com Google</span>
                </button>
                </form>
            </div>

            <!-- Cadastro Form -->
            <div id="cadastroFormContainer" class="hidden">
                <h1 class="text-2xl font-bold text-purple-600 text-center mb-6">Crie a sua conta</h1>
                <form id="preRegistrationForm" class="space-y-6">
                    <!-- Nome Completo -->
                    <div>
                        <label for="nomeCompleto" class="block text-sm font-medium text-gray-700 mb-2">
                            Nome completo
                        </label>
                        <input 
                            type="text" 
                            id="nomeCompleto" 
                            name="nomeCompleto"
                            placeholder="Coloque seu nome completo"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                        >
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="emailCadastro" class="block text-sm font-medium text-gray-700 mb-2">
                            Email
                        </label>
                        <input 
                            type="email" 
                            id="emailCadastro" 
                            name="email"
                            placeholder="Coloque seu endereço de Email"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                        >
                    </div>

                    <!-- Número de Celular -->
                    <div>
                        <label for="celular" class="block text-sm font-medium text-gray-700 mb-2">
                            Número de celular
                        </label>
                        <input 
                            type="tel" 
                            id="celular" 
                            name="celular"
                            placeholder="Coloque o seu número de celular"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                        >
                    </div>

                    <!-- Tipo de Cadastro -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Você é:
                        </label>
                        <div class="space-y-2">
                            <label class="flex items-center cursor-pointer">
                                <input 
                                    type="radio" 
                                    name="tipoCadastro" 
                                    value="pcd"
                                    required
                                    class="w-4 h-4 text-purple-600 focus:ring-purple-500"
                                >
                                <span class="ml-2 text-gray-700">Pessoa com Deficiência (PCD)</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input 
                                    type="radio" 
                                    name="tipoCadastro" 
                                    value="empresa"
                                    class="w-4 h-4 text-purple-600 focus:ring-purple-500"
                                >
                                <span class="ml-2 text-gray-700">Empresa</span>
                            </label>
                        </div>
                    </div>

                    <!-- Terms Checkbox -->
                    <div>
                        <label class="flex items-start cursor-pointer">
                            <input 
                                type="checkbox" 
                                id="termsAccepted"
                                required
                                class="w-4 h-4 mt-1 text-purple-600 focus:ring-purple-500 rounded"
                            >
                            <span class="ml-2 text-sm text-gray-700">
                                Eu concordo com os 
                                <a href="#" class="text-purple-600 hover:underline">Termos de serviço</a> 
                                e a 
                                <a href="#" class="text-purple-600 hover:underline">Política de Privacidade</a>
                            </span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit"
                        class="w-full bg-purple-600 text-white py-3 rounded-lg font-medium hover:bg-purple-700 transition"
                    >
                        Criar conta
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Switch between Login and Cadastro tabs
        function switchTab(tab) {
            const loginTab = document.getElementById('loginTab');
            const cadastroTab = document.getElementById('cadastroTab');
            const loginContainer = document.getElementById('loginFormContainer');
            const cadastroContainer = document.getElementById('cadastroFormContainer');

            if (tab === 'login') {
                loginTab.classList.add('text-purple-600', 'border-purple-600', 'border-b-2');
                loginTab.classList.remove('text-gray-500');
                cadastroTab.classList.remove('text-purple-600', 'border-purple-600', 'border-b-2');
                cadastroTab.classList.add('text-gray-500');
                loginContainer.classList.remove('hidden');
                cadastroContainer.classList.add('hidden');
            } else {
                cadastroTab.classList.add('text-purple-600', 'border-purple-600', 'border-b-2');
                cadastroTab.classList.remove('text-gray-500');
                loginTab.classList.remove('text-purple-600', 'border-purple-600', 'border-b-2');
                loginTab.classList.add('text-gray-500');
                cadastroContainer.classList.remove('hidden');
                loginContainer.classList.add('hidden');
            }
        }

        // Handle Google login
        function loginWithGoogle() {
            alert('Funcionalidade de login com Google será implementada em breve!');
            // In a real implementation, this would integrate with Google OAuth
        }

        // Handle pre-registration form submission
        document.getElementById('preRegistrationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = {
                nomeCompleto: document.getElementById('nomeCompleto').value,
                email: document.getElementById('emailCadastro').value,
                celular: document.getElementById('celular').value,
                tipoCadastro: document.querySelector('input[name="tipoCadastro"]:checked').value,
                termsAccepted: document.getElementById('termsAccepted').checked,
                timestamp: new Date().toISOString()
            };
            
            // Save to localStorage
            localStorage.setItem('preRegistrationData', JSON.stringify(formData));
            
            // Redirect based on type
            if (formData.tipoCadastro === 'empresa') {
                window.location.href = 'cadastro-empresa.php';
            } else {
                window.location.href = 'cadastro.php';
            }
        });

        // Phone mask
        document.getElementById('celular').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 11) value = value.slice(0, 11);
            
            if (value.length > 6) {
                value = value.replace(/^(\d{2})(\d{5})(\d{0,4}).*/, '($1) $2-$3');
            } else if (value.length > 2) {
                value = value.replace(/^(\d{2})(\d{0,5})/, '($1) $2');
            } else if (value.length > 0) {
                value = value.replace(/^(\d*)/, '($1');
            }
            
            e.target.value = value;
        });

        // Check URL parameter to switch to cadastro tab
        if (window.location.search.includes('cadastro=true')) {
            switchTab('cadastro');
        }
    </script>
</body>
</html>
