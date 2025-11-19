<?php
// Iniciar sessão para exibir mensagens
require_once 'config/auth.php';
startSecureSession();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Vigged</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-purple-600 text-white h-16">
        <div class="max-w-7xl mx-auto px-4 h-full flex items-center justify-between">
            <a href="index.php" class="text-2xl font-bold">Vigged</a>
            <div class="hidden md:flex space-x-6">
                <a href="index.php" class="hover:text-purple-200 transition">Início</a>
                <a href="vagas.php" class="hover:text-purple-200 transition">Vagas</a>
                <a href="empresas.php" class="hover:text-purple-200 transition">Empresas</a>
                <a href="sobre-nos.php" class="hover:text-purple-200 transition">Sobre nós</a>
                <a href="suporte.php" class="hover:text-purple-200 transition">Contato</a>
            </div>
            <div class="flex space-x-3">
                <a href="login.php" class="px-4 py-2 border border-white rounded-lg hover:bg-white hover:text-purple-600 transition">Login</a>
                <a href="pre-cadastro.php" class="px-4 py-2 bg-white text-purple-600 rounded-lg hover:bg-purple-50 transition">Cadastrar-se</a>
            </div>
        </div>
    </nav>

    <!-- Login Form -->
    <div class="min-h-screen flex items-center justify-center py-12 px-4">
        <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md">
            <h1 class="text-3xl font-bold text-purple-600 text-center mb-8">Bem vindo de volta!</h1>
            
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

            <!-- Sign Up Link -->
            <div class="mt-6 text-center">
                <p class="text-gray-600">
                    Não tem uma conta? 
                    <a href="pre-cadastro.php" class="text-purple-600 hover:text-purple-700 font-medium">Cadastre-se</a>
                </p>
            </div>
        </div>
    </div>



    <script>
        // Handle Google login
        function loginWithGoogle() {
            alert('Funcionalidade de login com Google será implementada em breve!');
            // In a real implementation, this would integrate with Google OAuth
        }
    </script>
</body>
</html>
