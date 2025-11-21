<?php
// Configurar título da página
$title = 'Criar Conta';

// Incluir head
include 'includes/head.php';

// Incluir navegação pública
$navType = 'public';
include 'includes/nav.php';
?>

    <!-- Main Content -->
    <main class="max-w-6xl mx-auto px-6 py-12">
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="grid md:grid-cols-2 gap-0">
                <!-- Left Side - Form -->
                <div class="p-8 md:p-12">
                    <h2 class="text-3xl font-bold text-purple-600 mb-8">Crie a sua conta</h2>
                    
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
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email
                            </label>
                            <input 
                                type="email" 
                                id="email" 
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

                        <!-- PCD Question -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Acompanha PCD(Pessoa com deficiência)?
                            </label>
                            <div class="flex space-x-6">
                                <label class="flex items-center cursor-pointer">
                                    <input 
                                        type="radio" 
                                        name="isPCD" 
                                        value="sim"
                                        required
                                        class="w-4 h-4 text-purple-600 focus:ring-purple-500"
                                    >
                                    <span class="ml-2 text-gray-700">Sim</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input 
                                        type="radio" 
                                        name="isPCD" 
                                        value="nao"
                                        class="w-4 h-4 text-purple-600 focus:ring-purple-500"
                                    >
                                    <span class="ml-2 text-gray-700">Não</span>
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
                            class="w-full md:w-auto px-8 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-medium text-lg"
                        >
                            Criar conta
                        </button>
                    </form>
                </div>

                <!-- Right Side - Info Box -->
                <div class="bg-gradient-to-br from-purple-600 to-purple-700 p-8 md:p-12 flex flex-col justify-center text-white">
                    <h3 class="text-3xl font-bold mb-6 text-center">
                        Cadastro Rápido e Acessível
                    </h3>
                    <p class="text-lg text-center mb-12 leading-relaxed">
                        Crie sua conta em poucos passos e tenha acesso imediato a vagas inclusivas, empresas comprometidas com a diversidade e recursos pensados para você. Tudo de forma segura, simples e acessível.
                    </p>
                    
                    <!-- Features -->
                    <div class="grid grid-cols-3 gap-6">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mb-3">
                                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <span class="font-semibold">Seguro</span>
                        </div>
                        
                        <div class="flex flex-col items-center text-center">
                            <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mb-3">
                                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <span class="font-semibold">Rápido</span>
                        </div>
                        
                        <div class="flex flex-col items-center text-center">
                            <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mb-3">
                                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                                </svg>
                            </div>
                            <span class="font-semibold">Privado</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-100 mt-20">
        <div class="max-w-7xl mx-auto px-6 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Logo and Social -->
                <div>
                    <h2 class="text-2xl font-bold text-purple-600 mb-4">Vigged</h2>
                    <p class="text-gray-600 text-sm mb-4">
                        Conectando talentos PCD às melhores oportunidades do mercado.
                    </p>
                    <div class="flex space-x-3">
                        <a href="#" class="text-purple-600 hover:text-purple-700">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="#" class="text-purple-600 hover:text-purple-700">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                        </a>
                        <a href="#" class="text-purple-600 hover:text-purple-700">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c0 .21 0 .42-.015.63A9.935 9.935 0 0024 4.59z"/></svg>
                        </a>
                        <a href="#" class="text-purple-600 hover:text-purple-700">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c0 .28.015.667.072 1.053a9.935 9.935 0 002.913 2.913c.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.766-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.072 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42 2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/></svg>
                        </a>
                        <a href="#" class="text-purple-600 hover:text-purple-700">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Links Columns -->
                <div>
                    <h3 class="font-semibold text-gray-900 mb-4">Empresa</h3>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li><a href="#" class="hover:text-purple-600">Sobre nós</a></li>
                        <li><a href="#" class="hover:text-purple-600">Carreiras</a></li>
                        <li><a href="#" class="hover:text-purple-600">Blog</a></li>
                        <li><a href="#" class="hover:text-purple-600">Contato</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-semibold text-gray-900 mb-4">Recursos</h3>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li><a href="#" class="hover:text-purple-600">Central de Ajuda</a></li>
                        <li><a href="#" class="hover:text-purple-600">Guias</a></li>
                        <li><a href="#" class="hover:text-purple-600">Webinars</a></li>
                        <li><a href="#" class="hover:text-purple-600">Comunidade</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-semibold text-gray-900 mb-4">Legal</h3>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li><a href="#" class="hover:text-purple-600">Termos de Uso</a></li>
                        <li><a href="#" class="hover:text-purple-600">Privacidade</a></li>
                        <li><a href="#" class="hover:text-purple-600">Cookies</a></li>
                        <li><a href="#" class="hover:text-purple-600">Licenças</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-300 mt-8 pt-8 text-center text-sm text-gray-600">
                <p>&copy; 2025 Vigged. Todos os direitos reservados.</p>
            </div>
        </div>
<?php
// Incluir footer padrão
include 'includes/footer.php';
?>

    <script>
        // Form submission handler
        document.getElementById('preRegistrationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = {
                nomeCompleto: document.getElementById('nomeCompleto').value,
                email: document.getElementById('email').value,
                celular: document.getElementById('celular').value,
                isPCD: document.querySelector('input[name="isPCD"]:checked').value,
                termsAccepted: document.getElementById('termsAccepted').checked,
                timestamp: new Date().toISOString()
            };
            
            // Save to localStorage
            localStorage.setItem('preRegistrationData', JSON.stringify(formData));
            
            // Redirect to full registration
            window.location.href = 'cadastro.php';
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
    </script>
