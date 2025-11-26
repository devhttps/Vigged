<?php
// Configurar título da página
$title = 'Suporte';

// Iniciar sessão para manter autenticação
require_once 'config/auth.php';
startSecureSession();

// Incluir head
include 'includes/head.php';

// Incluir navegação (será determinada automaticamente pela autenticação)
include 'includes/nav.php';
?>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <!-- Heading -->
        <h1 class="text-5xl font-bold text-purple-600 text-center mb-16">Fale com nosso suporte</h1>

        <!-- Contact Form -->
        <form id="supportForm" class="max-w-6xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Left Column -->
                <div class="space-y-6">
                    <!-- Nome -->
                    <div>
                        <label for="nome" class="block text-gray-900 font-semibold mb-2">Nome</label>
                        <input 
                            type="text" 
                            id="nome" 
                            name="nome" 
                            placeholder="Coloque seu nome completo"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                            required
                        >
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-gray-900 font-semibold mb-2">Email</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            placeholder="Coloque seu endereço de Email"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                            required
                        >
                    </div>

                    <!-- Assunto -->
                    <div>
                        <label for="assunto" class="block text-gray-900 font-semibold mb-2">Assunto</label>
                        <input 
                            type="text" 
                            id="assunto" 
                            name="assunto" 
                            placeholder="Coloque o assunto que deseja tratar"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent"
                            required
                        >
                    </div>
                </div>

                <!-- Right Column -->
                <div>
                    <label for="mensagem" class="block text-gray-900 font-semibold mb-2">Mensagem</label>
                    <textarea 
                        id="mensagem" 
                        name="mensagem" 
                        rows="10"
                        placeholder="Descreva sua dúvida ou problema..."
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent resize-none"
                        required
                    ></textarea>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-8 text-center">
                <button 
                    type="submit" 
                    class="bg-purple-600 text-white px-12 py-3 rounded-lg text-lg font-semibold hover:bg-purple-700 transition"
                >
                    Enviar Mensagem
                </button>
            </div>
        </form>

        <!-- Success Message (hidden by default) -->
        <div id="successMessage" class="hidden max-w-6xl mx-auto mt-8 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg text-center">
            Mensagem enviada com sucesso! Entraremos em contato em breve.
        </div>
    </main>
    
    <script>
        // Form submission handler
        document.getElementById('supportForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = {
                nome: document.getElementById('nome').value,
                email: document.getElementById('email').value,
                assunto: document.getElementById('assunto').value,
                mensagem: document.getElementById('mensagem').value,
                timestamp: new Date().toISOString()
            };
            
            // Save to localStorage (in a real app, this would be sent to a server)
            let supportMessages = JSON.parse(localStorage.getItem('supportMessages') || '[]');
            supportMessages.push(formData);
            localStorage.setItem('supportMessages', JSON.stringify(supportMessages));
            
            // Show success message
            document.getElementById('successMessage').classList.remove('hidden');
            
            // Reset form
            this.reset();
            
            // Hide success message after 5 seconds
            setTimeout(() => {
                document.getElementById('successMessage').classList.add('hidden');
            }, 5000);
        });
    </script>
<?php
// Incluir footer padrão
include 'includes/footer.php';
?>
