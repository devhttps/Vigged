<?php
// Configurar título da página
$title = 'Cadastro de Empresa';

// Estilos e recursos adicionais (Font Awesome)
$additionalStyles = [
    '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">'
];

// Incluir head
include 'includes/head.php';

// Incluir navegação pública
$navType = 'public';
include 'includes/nav.php';
?>

    <!-- Form Container -->
    <div class="max-w-5xl mx-auto px-4 py-12">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-purple-600 mb-2">Cadastro de Empresa</h1>
            <p class="text-gray-600">Cadastre sua empresa e conecte-se com talentos PCD qualificados. Campos marcados com * são obrigatórios.</p>
        </div>

        <form action="processar_cadastro_empresa.php" method="POST" enctype="multipart/form-data" class="space-y-8">
            <?php
            // Adicionar token CSRF
            require_once 'includes/csrf.php';
            echo csrfField();
            ?>
            <!-- Company Information Section -->
            <div class="bg-white rounded-lg shadow-sm p-8">
                <h2 class="text-2xl font-bold text-purple-600 mb-6">Informações da Empresa</h2>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="razao_social" class="block text-sm font-medium text-gray-700 mb-2">
                            Razão Social *
                        </label>
                        <input 
                            type="text" 
                            id="razao_social" 
                            name="razao_social" 
                            required 
                            placeholder="Digite a razão social da empresa"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        >
                    </div>

                    <div>
                        <label for="nome_fantasia" class="block text-sm font-medium text-gray-700 mb-2">
                            Nome Fantasia *
                        </label>
                        <input 
                            type="text" 
                            id="nome_fantasia" 
                            name="nome_fantasia" 
                            required 
                            placeholder="Nome comercial da empresa"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        >
                    </div>

                    <div>
                        <label for="cnpj" class="block text-sm font-medium text-gray-700 mb-2">
                            CNPJ *
                        </label>
                        <input 
                            type="text" 
                            id="cnpj" 
                            name="cnpj" 
                            required 
                            placeholder="00.000.000/0000-00"
                            maxlength="18"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        >
                    </div>

<div>
    <label for="data_fundacao" class="block text-sm font-medium text-gray-700 mb-2">
        Data de Fundação
    </label>

    <input 
        type="date"
        id="data_fundacao"
        name="data_fundacao"
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
    >
</div>


                    <div>
                        <label for="porte_empresa" class="block text-sm font-medium text-gray-700 mb-2">
                            Porte da Empresa *
                        </label>
                        <select 
                            id="porte_empresa" 
                            name="porte_empresa" 
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        >
                            <option value="">Selecione</option>
                            <option value="mei">MEI</option>
                            <option value="micro">Microempresa (até 19 funcionários)</option>
                            <option value="pequena">Pequena (20 a 99 funcionários)</option>
                            <option value="media">Média (100 a 499 funcionários)</option>
                            <option value="grande">Grande (500+ funcionários)</option>
                        </select>
                    </div>

                    <div>
                        <label for="setor" class="block text-sm font-medium text-gray-700 mb-2">
                            Setor de Atuação *
                        </label>
                        <select 
                            id="setor" 
                            name="setor" 
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        >
                            <option value="">Selecione</option>
                            <option value="tecnologia">Tecnologia</option>
                            <option value="financeiro">Financeiro</option>
                            <option value="saude">Saúde</option>
                            <option value="educacao">Educação</option>
                            <option value="varejo">Varejo</option>
                            <option value="industria">Indústria</option>
                            <option value="servicos">Serviços</option>
                            <option value="construcao">Construção</option>
                            <option value="alimentos">Alimentos e Bebidas</option>
                            <option value="outro">Outro</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label for="website" class="block text-sm font-medium text-gray-700 mb-2">
                            Website
                        </label>
                        <input 
                            type="url" 
                            id="website" 
                            name="website" 
                            placeholder="https://www.suaempresa.com.br"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        >
                    </div>

                    <div class="md:col-span-2">
                        <label for="descricao" class="block text-sm font-medium text-gray-700 mb-2">
                            Descrição da Empresa *
                        </label>
                        <textarea 
                            id="descricao" 
                            name="descricao" 
                            required
                            rows="4"
                            placeholder="Descreva brevemente sua empresa, missão e valores"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        ></textarea>
                    </div>
                </div>
            </div>

            <!-- Address Section -->
            <div class="bg-white rounded-lg shadow-sm p-8">
                <h2 class="text-2xl font-bold text-purple-600 mb-6">Endereço</h2>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label for="cep" class="block text-sm font-medium text-gray-700 mb-2">
                            CEP *
                        </label>
                        <input 
                            type="text" 
                            id="cep" 
                            name="cep" 
                            required 
                            placeholder="00000-000"
                            maxlength="9"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        >
                    </div>

                    <div>
                        <label for="estado" class="block text-sm font-medium text-gray-700 mb-2">
                            Estado *
                        </label>
                        <select 
                            id="estado" 
                            name="estado" 
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        >
                            <option value="">Selecione</option>
                            <option value="AC">Acre</option>
                            <option value="AL">Alagoas</option>
                            <option value="AP">Amapá</option>
                            <option value="AM">Amazonas</option>
                            <option value="BA">Bahia</option>
                            <option value="CE">Ceará</option>
                            <option value="DF">Distrito Federal</option>
                            <option value="ES">Espírito Santo</option>
                            <option value="GO">Goiás</option>
                            <option value="MA">Maranhão</option>
                            <option value="MT">Mato Grosso</option>
                            <option value="MS">Mato Grosso do Sul</option>
                            <option value="MG">Minas Gerais</option>
                            <option value="PA">Pará</option>
                            <option value="PB">Paraíba</option>
                            <option value="PR">Paraná</option>
                            <option value="PE">Pernambuco</option>
                            <option value="PI">Piauí</option>
                            <option value="RJ">Rio de Janeiro</option>
                            <option value="RN">Rio Grande do Norte</option>
                            <option value="RS">Rio Grande do Sul</option>
                            <option value="RO">Rondônia</option>
                            <option value="RR">Roraima</option>
                            <option value="SC">Santa Catarina</option>
                            <option value="SP">São Paulo</option>
                            <option value="SE">Sergipe</option>
                            <option value="TO">Tocantins</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label for="cidade" class="block text-sm font-medium text-gray-700 mb-2">
                            Cidade *
                        </label>
                        <input 
                            type="text" 
                            id="cidade" 
                            name="cidade" 
                            required 
                            placeholder="Digite a cidade"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        >
                    </div>

                    <div class="md:col-span-2">
                        <label for="bairro" class="block text-sm font-medium text-gray-700 mb-2">
                            Bairro *
                        </label>
                        <input 
                            type="text" 
                            id="bairro" 
                            name="bairro" 
                            required 
                            placeholder="Digite o bairro"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        >
                    </div>

                    <div class="md:col-span-2">
                        <label for="logradouro" class="block text-sm font-medium text-gray-700 mb-2">
                            Logradouro *
                        </label>
                        <input 
                            type="text" 
                            id="logradouro" 
                            name="logradouro" 
                            required 
                            placeholder="Rua, Avenida, etc."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        >
                    </div>

                    <div>
                        <label for="numero" class="block text-sm font-medium text-gray-700 mb-2">
                            Número *
                        </label>
                        <input 
                            type="text" 
                            id="numero" 
                            name="numero" 
                            required 
                            placeholder="Nº"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        >
                    </div>

                    <div>
                        <label for="complemento" class="block text-sm font-medium text-gray-700 mb-2">
                            Complemento
                        </label>
                        <input 
                            type="text" 
                            id="complemento" 
                            name="complemento" 
                            placeholder="Sala, Andar, etc."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        >
                    </div>
                </div>
            </div>

            <!-- Contact Information Section -->
            <div class="bg-white rounded-lg shadow-sm p-8">
                <h2 class="text-2xl font-bold text-purple-600 mb-6">Informações de Contato</h2>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label for="email_corporativo" class="block text-sm font-medium text-gray-700 mb-2">
                            E-mail Corporativo *
                        </label>
                        <input 
                            type="email" 
                            id="email_corporativo" 
                            name="email_corporativo" 
                            required 
                            placeholder="contato@empresa.com.br"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        >
                    </div>

                    <div>
                        <label for="telefone_empresa" class="block text-sm font-medium text-gray-700 mb-2">
                            Telefone *
                        </label>
                        <input 
                            type="tel" 
                            id="telefone_empresa" 
                            name="telefone_empresa" 
                            required 
                            placeholder="(00) 0000-0000"
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

            <!-- Responsible Person Section -->
            <div class="bg-white rounded-lg shadow-sm p-8">
                <h2 class="text-2xl font-bold text-purple-600 mb-6">Responsável pelo Cadastro</h2>
                
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="nome_responsavel" class="block text-sm font-medium text-gray-700 mb-2">
                            Nome Completo *
                        </label>
                        <input 
                            type="text" 
                            id="nome_responsavel" 
                            name="nome_responsavel" 
                            required 
                            placeholder="Digite o nome completo"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        >
                    </div>

                    <div>
                        <label for="cargo_responsavel" class="block text-sm font-medium text-gray-700 mb-2">
                            Cargo *
                        </label>
                        <input 
                            type="text" 
                            id="cargo_responsavel" 
                            name="cargo_responsavel" 
                            required 
                            placeholder="Ex: Gerente de RH"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        >
                    </div>

                    <div>
                        <label for="email_responsavel" class="block text-sm font-medium text-gray-700 mb-2">
                            E-mail *
                        </label>
                        <input 
                            type="email" 
                            id="email_responsavel" 
                            name="email_responsavel" 
                            required 
                            placeholder="email@empresa.com.br"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        >
                    </div>

                    <div>
                        <label for="telefone_responsavel" class="block text-sm font-medium text-gray-700 mb-2">
                            Telefone *
                        </label>
                        <input 
                            type="tel" 
                            id="telefone_responsavel" 
                            name="telefone_responsavel" 
                            required 
                            placeholder="(00) 00000-0000"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        >
                    </div>
                </div>
            </div>

            <!-- Inclusion Commitment Section -->
            <div class="bg-white rounded-lg shadow-sm p-8">
                <h2 class="text-2xl font-bold text-purple-600 mb-6">Compromisso com a Inclusão</h2>
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Sua empresa já contrata pessoas com deficiência? *
                        </label>
                        <div class="flex space-x-6">
                            <label class="flex items-center">
                                <input type="radio" name="ja_contrata_pcd" value="sim" class="text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-gray-700">Sim</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="ja_contrata_pcd" value="nao" class="text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-gray-700">Não</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Sua empresa possui recursos de acessibilidade?
                        </label>
                        <div class="grid md:grid-cols-2 gap-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="recursos_acessibilidade[]" value="rampas" class="text-purple-600 focus:ring-purple-500 rounded">
                                <span class="ml-2 text-gray-700">Rampas de acesso</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="recursos_acessibilidade[]" value="elevadores" class="text-purple-600 focus:ring-purple-500 rounded">
                                <span class="ml-2 text-gray-700">Elevadores adaptados</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="recursos_acessibilidade[]" value="banheiros" class="text-purple-600 focus:ring-purple-500 rounded">
                                <span class="ml-2 text-gray-700">Banheiros acessíveis</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="recursos_acessibilidade[]" value="vagas_estacionamento" class="text-purple-600 focus:ring-purple-500 rounded">
                                <span class="ml-2 text-gray-700">Vagas de estacionamento</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="recursos_acessibilidade[]" value="tecnologia_assistiva" class="text-purple-600 focus:ring-purple-500 rounded">
                                <span class="ml-2 text-gray-700">Tecnologia assistiva</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="recursos_acessibilidade[]" value="interprete_libras" class="text-purple-600 focus:ring-purple-500 rounded">
                                <span class="ml-2 text-gray-700">Intérprete de Libras</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label for="politica_inclusao" class="block text-sm font-medium text-gray-700 mb-2">
                            Descreva a política de inclusão da sua empresa
                        </label>
                        <textarea 
                            id="politica_inclusao" 
                            name="politica_inclusao" 
                            rows="4"
                            placeholder="Conte-nos sobre as iniciativas e práticas de inclusão da sua empresa"
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
                            Contrato Social ou Documento de Constituição (PDF - máx. 10MB)
                        </label>
                        <div class="flex items-center space-x-4">
                            <label for="documento_empresa" class="px-6 py-2 border-2 border-purple-600 text-purple-600 rounded-lg hover:bg-purple-50 cursor-pointer transition">
                                Escolher arquivo
                            </label>
                            <span id="doc-file-name" class="text-gray-500 text-sm">Nenhum arquivo selecionado</span>
                            <input 
                                type="file" 
                                id="documento_empresa" 
                                name="documento_empresa" 
                                accept=".pdf"
                                class="hidden"
                            >
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Logo da Empresa (PNG, JPG - máx. 5MB)
                        </label>
                        <div class="flex items-center space-x-4">
                            <label for="logo_empresa" class="px-6 py-2 border-2 border-purple-600 text-purple-600 rounded-lg hover:bg-purple-50 cursor-pointer transition">
                                Escolher arquivo
                            </label>
                            <span id="logo-file-name" class="text-gray-500 text-sm">Nenhum arquivo selecionado</span>
                            <input 
                                type="file" 
                                id="logo_empresa" 
                                name="logo_empresa" 
                                accept=".png,.jpg,.jpeg"
                                class="hidden"
                            >
                        </div>
                    </div>

                    <div class="pt-4">
                        <label class="flex items-start">
                            <input type="checkbox" name="aceita_termos" required class="mt-1 text-purple-600 focus:ring-purple-500 rounded">
                            <span class="ml-2 text-gray-700">
                                Concordo com os 
                                <a href="termos.php" class="text-purple-600 hover:underline">termos de uso</a> 
                                e 
                                <a href="politica.php" class="text-purple-600 hover:underline">política de privacidade</a>
                                e confirmo que as informações fornecidas são verdadeiras
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-4">
                <button 
                    type="button" 
                    onclick="window.location.href='index.html'"
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

    <script>
        // File upload displays
        document.getElementById('documento_empresa').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name || 'Nenhum arquivo selecionado';
            document.getElementById('doc-file-name').textContent = fileName;
        });

        document.getElementById('logo_empresa').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name || 'Nenhum arquivo selecionado';
            document.getElementById('logo-file-name').textContent = fileName;
        });

        // CNPJ mask
        document.getElementById('cnpj').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 14) {
                value = value.replace(/(\d{2})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1/$2');
                value = value.replace(/(\d{4})(\d{1,2})$/, '$1-$2');
                e.target.value = value;
            }
        });

        // CEP mask
        document.getElementById('cep').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 8) {
                value = value.replace(/(\d{5})(\d)/, '$1-$2');
                e.target.value = value;
            }
        });

        // Phone masks
        document.getElementById('telefone_empresa').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 10) {
                value = value.replace(/(\d{2})(\d)/, '($1) $2');
                value = value.replace(/(\d{4})(\d)/, '$1-$2');
            }
            e.target.value = value;
        });

        document.getElementById('telefone_responsavel').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 11) {
                value = value.replace(/(\d{2})(\d)/, '($1) $2');
                value = value.replace(/(\d{5})(\d)/, '$1-$2');
            }
            e.target.value = value;
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
        });
    </script>
<?php
// Incluir footer padrão
include 'includes/footer.php';
?>
