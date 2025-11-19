<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Usuário PCD - Vigged</title>
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

    <!-- Form Container -->
    <div class="max-w-5xl mx-auto px-4 py-12">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-purple-600 mb-2">Cadastro de Usuário PCD</h1>
            <p class="text-gray-600">Preencha o formulário abaixo com suas informações. Campos marcados com * são obrigatórios.</p>
        </div>

        <?php
        // Iniciar sessão para exibir erros
        require_once 'config/auth.php';
        startSecureSession();
        
        // Exibir erros de cadastro
        if (isset($_SESSION['cadastro_errors']) && !empty($_SESSION['cadastro_errors'])) {
            echo '<div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">';
            echo '<p class="font-bold mb-2">Erros encontrados:</p>';
            echo '<ul class="list-disc list-inside">';
            foreach ($_SESSION['cadastro_errors'] as $error) {
                echo '<li>' . htmlspecialchars($error) . '</li>';
            }
            echo '</ul>';
            echo '</div>';
            unset($_SESSION['cadastro_errors']);
        }
        
        // Exibir mensagem de sucesso
        if (isset($_SESSION['cadastro_success']) && $_SESSION['cadastro_success']) {
            echo '<div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">';
            echo '<p>' . htmlspecialchars($_SESSION['cadastro_message'] ?? 'Cadastro realizado com sucesso!') . '</p>';
            echo '</div>';
            unset($_SESSION['cadastro_success']);
            unset($_SESSION['cadastro_message']);
        }
        ?>

        <form action="processar_cadastro.php" method="POST" enctype="multipart/form-data" class="space-y-8">
            <!-- Personal Information Section -->
            <div class="bg-white rounded-lg shadow-sm p-8">
                <h2 class="text-2xl font-bold text-purple-600 mb-6">Informações Pessoais</h2>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="nome" class="block text-sm font-medium text-gray-700 mb-2">
                            Nome Completo *
                        </label>
                        <input 
                            type="text" 
                            id="nome" 
                            name="nome" 
                            required 
                            placeholder="Digite seu nome completo"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        >
                    </div>

                    <div>
                        <label for="cpf" class="block text-sm font-medium text-gray-700 mb-2">
                            CPF *
                        </label>
                        <input 
                            type="text" 
                            id="cpf" 
                            name="cpf" 
                            required 
                            placeholder="000.000.000-00"
                            maxlength="14"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        >
                    </div>

                    <div>
                        <label for="data_nascimento" class="block text-sm font-medium text-gray-700 mb-2">
                            Data de Nascimento *
                        </label>
                        <input 
                            type="text" 
                            id="data_nascimento" 
                            name="data_nascimento" 
                            required 
                            placeholder="DD/MM/AAAA"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        >
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            E-mail *
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            required 
                            placeholder="seu_email@exemplo.com"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        >
                    </div>

                    <div>
                        <label for="telefone" class="block text-sm font-medium text-gray-700 mb-2">
                            Telefone *
                        </label>
                        <input 
                            type="tel" 
                            id="telefone" 
                            name="telefone" 
                            required 
                            placeholder="(00) 00000-0000"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        >
                    </div>

                    <div>
                        <label for="senha" class="block text-sm font-medium text-gray-700 mb-2">
                            Senha *
                        </label>
                        <input 
                            type="password" 
                            id="senha" 
                            name="senha" 
                            required 
                            minlength="6"
                            placeholder="Mínimo 6 caracteres"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        >
                        <p class="text-xs text-gray-500 mt-1">A senha deve ter no mínimo 6 caracteres</p>
                    </div>

                    <div>
                        <label for="confirmar_senha" class="block text-sm font-medium text-gray-700 mb-2">
                            Confirmar Senha *
                        </label>
                        <input 
                            type="password" 
                            id="confirmar_senha" 
                            name="confirmar_senha" 
                            required 
                            minlength="6"
                            placeholder="Digite a senha novamente"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        >
                    </div>
                </div>
            </div>

            <!-- Disability Information Section -->
            <div class="bg-white rounded-lg shadow-sm p-8">
                <h2 class="text-2xl font-bold text-purple-600 mb-6">Informações sobre Deficiência</h2>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Tipo de Deficiência *
                        </label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="tipo_deficiencia" value="fisica" class="text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-gray-700">Deficiência Física</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="tipo_deficiencia" value="visual" class="text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-gray-700">Deficiência Visual</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="tipo_deficiencia" value="auditiva" class="text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-gray-700">Deficiência Auditiva</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="tipo_deficiencia" value="intelectual" class="text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-gray-700">Deficiência Intelectual</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="tipo_deficiencia" value="multipla" class="text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-gray-700">Deficiência Múltipla</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="tipo_deficiencia" value="tea" class="text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-gray-700">Transtorno do Espectro Autista</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="tipo_deficiencia" value="outra" class="text-purple-600 focus:ring-purple-500" id="outra_deficiencia">
                                <span class="ml-2 text-gray-700">Outra</span>
                            </label>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label for="especifique_outra" class="block text-sm font-medium text-gray-700 mb-2">
                                Especifique (se selecionou "Outra")
                            </label>
                            <input 
                                type="text" 
                                id="especifique_outra" 
                                name="especifique_outra" 
                                placeholder="Descreva sua deficiência"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            >
                        </div>

                        <div>
                            <label for="cid" class="block text-sm font-medium text-gray-700 mb-2">
                                CID (Classificação Internacional de Doenças)
                            </label>
                            <input 
                                type="text" 
                                id="cid" 
                                name="cid" 
                                placeholder="Ex: F84.0"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            >
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Possui laudo médico? *
                            </label>
                            <div class="flex space-x-6">
                                <label class="flex items-center">
                                    <input type="radio" name="possui_laudo" value="sim" class="text-purple-600 focus:ring-purple-500">
                                    <span class="ml-2 text-gray-700">Sim</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="possui_laudo" value="nao" class="text-purple-600 focus:ring-purple-500">
                                    <span class="ml-2 text-gray-700">Não</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Accessibility Needs Section -->
            <div class="bg-white rounded-lg shadow-sm p-8">
                <h2 class="text-2xl font-bold text-purple-600 mb-6">Necessidades de Acessibilidade</h2>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Recursos de acessibilidade que você utiliza:
                    </label>
                    <div class="grid md:grid-cols-2 gap-4 mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="recursos[]" value="leitor_tela" class="text-purple-600 focus:ring-purple-500 rounded">
                            <span class="ml-2 text-gray-700">Leitor de tela</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="recursos[]" value="interprete_libras" class="text-purple-600 focus:ring-purple-500 rounded">
                            <span class="ml-2 text-gray-700">Intérprete de Libras</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="recursos[]" value="material_braille" class="text-purple-600 focus:ring-purple-500 rounded">
                            <span class="ml-2 text-gray-700">Material em Braille</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="recursos[]" value="alto_contraste" class="text-purple-600 focus:ring-purple-500 rounded">
                            <span class="ml-2 text-gray-700">Alto contraste</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="recursos[]" value="fonte_ampliada" class="text-purple-600 focus:ring-purple-500 rounded">
                            <span class="ml-2 text-gray-700">Fonte ampliada</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="recursos[]" value="cadeira_rodas" class="text-purple-600 focus:ring-purple-500 rounded">
                            <span class="ml-2 text-gray-700">Cadeira de rodas</span>
                        </label>
                    </div>

                    <div>
                        <label for="outras_necessidades" class="block text-sm font-medium text-gray-700 mb-2">
                            Outras necessidades específicas:
                        </label>
                        <textarea 
                            id="outras_necessidades" 
                            name="outras_necessidades" 
                            rows="4"
                            placeholder="Descreva outras necessidades de acessibilidade que você possui"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        ></textarea>
                    </div>
                </div>
            </div>

            <!-- Documentation Section -->
            <div class="bg-white rounded-lg shadow-sm p-8">
                <h2 class="text-2xl font-bold text-purple-600 mb-6">Documentação</h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Anexar laudo médico (PDF, JPG ou PNG - máx. 5MB)
                        </label>
                        <div class="flex items-center space-x-4">
                            <label for="laudo_medico" class="px-6 py-2 border-2 border-purple-600 text-purple-600 rounded-lg hover:bg-purple-50 cursor-pointer transition">
                                Escolher arquivo
                            </label>
                            <span id="file-name" class="text-gray-500 text-sm">Nenhum arquivo selecionado</span>
                            <input 
                                type="file" 
                                id="laudo_medico" 
                                name="laudo_medico" 
                                accept=".pdf,.jpg,.jpeg,.png"
                                class="hidden"
                            >
                        </div>
                    </div>

                    <div class="pt-4">
                        <label class="flex items-start">
                            <input type="checkbox" name="aceita_termos" required class="mt-1 text-purple-600 focus:ring-purple-500 rounded">
                            <span class="ml-2 text-gray-700">
                                Concordo com os 
                                <a href="#" class="text-purple-600 hover:underline">termos de uso</a> 
                                e 
                                <a href="#" class="text-purple-600 hover:underline">política de privacidade</a>
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-4">
                <button 
                    type="button" 
                    onclick="window.location.href='index.php'"
                    class="px-8 py-3 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium"
                >
                    Cancelar
                </button>
                <button 
                    type="submit" 
                    class="px-8 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-medium"
                >
                    Finalizar Cadastro
                </button>
            </div>
        </form>
    </div>

<?php
// Incluir footer padrão
include 'includes/footer.php';
?>

    <script>
        window.addEventListener('DOMContentLoaded', function() {
            const preRegData = localStorage.getItem('preRegistrationData');
            
            if (preRegData) {
                try {
                    const data = JSON.parse(preRegData);
                    
                    // Pre-fill personal information fields
                    if (data.nomeCompleto) {
                        document.getElementById('nome').value = data.nomeCompleto;
                    }
                    if (data.email) {
                        document.getElementById('email').value = data.email;
                    }
                    if (data.celular) {
                        document.getElementById('telefone').value = data.celular;
                    }
                    
                    console.log('[v0] Pre-registration data loaded successfully');
                } catch (error) {
                    console.error('[v0] Error loading pre-registration data:', error);
                }
            }
        });

        // File upload display
        document.getElementById('laudo_medico').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name || 'Nenhum arquivo selecionado';
            document.getElementById('file-name').textContent = fileName;
        });

        // CPF mask
        document.getElementById('cpf').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 11) {
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
                e.target.value = value;
            }
        });

        // Phone mask
        document.getElementById('telefone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 11) {
                value = value.replace(/(\d{2})(\d)/, '($1) $2');
                value = value.replace(/(\d{5})(\d)/, '$1-$2');
                e.target.value = value;
            }
        });

        // Validação de senha
        document.querySelector('form').addEventListener('submit', function(e) {
            const senha = document.getElementById('senha').value;
            const confirmarSenha = document.getElementById('confirmar_senha').value;
            
            if (senha !== confirmarSenha) {
                e.preventDefault();
                alert('As senhas não coincidem. Por favor, verifique e tente novamente.');
                document.getElementById('confirmar_senha').focus();
                return false;
            }
            
            if (senha.length < 6) {
                e.preventDefault();
                alert('A senha deve ter no mínimo 6 caracteres.');
                document.getElementById('senha').focus();
                return false;
            }
            
            localStorage.removeItem('preRegistrationData');
            console.log('[v0] Pre-registration data cleared from localStorage');
        });
    </script>
</body>
</html>
