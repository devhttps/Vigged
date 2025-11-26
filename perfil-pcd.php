<?php
// Configurar título da página
$title = 'Meu Perfil';

// Incluir head
include 'includes/head.php';

// Incluir navegação autenticada
$navType = 'authenticated';
include 'includes/nav.php';

// Verificar autenticação
require_once 'config/auth.php';
startSecureSession();
requireAuth(USER_TYPE_PCD);

// Buscar dados do usuário
$currentUser = getCurrentUser();
$userData = $currentUser;

// Exibir mensagens de erro/sucesso
$errors = $_SESSION['perfil_errors'] ?? [];
$success = $_SESSION['perfil_success'] ?? null;
unset($_SESSION['perfil_errors'], $_SESSION['perfil_success']);

// Preparar dados para exibição
$fotoPerfil = 'https://via.placeholder.com/400x400?text=Sem+Foto';
if (!empty($userData['foto_perfil'])) {
    // Se for caminho relativo, adicionar BASE_URL
    if (strpos($userData['foto_perfil'], 'http') === 0) {
        $fotoPerfil = htmlspecialchars($userData['foto_perfil']);
    } else {
        $fotoPerfil = BASE_URL . '/' . htmlspecialchars($userData['foto_perfil']);
    }
}
$nome = htmlspecialchars($userData['nome'] ?? '');
$email = htmlspecialchars($userData['email'] ?? '');
$telefone = htmlspecialchars($userData['telefone'] ?? '');
$sobre = htmlspecialchars($userData['sobre'] ?? '');
$cidade = htmlspecialchars($userData['cidade'] ?? '');
$estado = htmlspecialchars($userData['estado'] ?? '');
$habilidades = !empty($userData['habilidades']) ? json_decode($userData['habilidades'], true) : [];
$experiencias = !empty($userData['experiencias']) ? json_decode($userData['experiencias'], true) : [];
$formacao = !empty($userData['formacao_academica']) ? json_decode($userData['formacao_academica'], true) : [];
$curriculoPath = null;
if (!empty($userData['curriculo_path'])) {
    // Se for caminho relativo, adicionar BASE_URL
    if (strpos($userData['curriculo_path'], 'http') === 0) {
        $curriculoPath = $userData['curriculo_path'];
    } else {
        $curriculoPath = BASE_URL . '/' . $userData['curriculo_path'];
    }
}

// Endereço completo
$enderecoCompleto = '';
if (!empty($userData['logradouro'])) {
    $enderecoCompleto = $userData['logradouro'];
    if (!empty($userData['numero'])) $enderecoCompleto .= ', ' . $userData['numero'];
    if (!empty($userData['bairro'])) $enderecoCompleto .= ' - ' . $userData['bairro'];
    if (!empty($userData['cidade'])) $enderecoCompleto .= ', ' . $userData['cidade'];
    if (!empty($userData['estado'])) $enderecoCompleto .= '/' . $userData['estado'];
    if (!empty($userData['cep'])) $enderecoCompleto .= ' - CEP: ' . $userData['cep'];
}
?>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Mensagens de erro/sucesso -->
        <?php if (!empty($errors)): ?>
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                <p class="font-bold mb-2">Erros encontrados:</p>
                <ul class="list-disc list-inside">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                <p><?php echo htmlspecialchars($success); ?></p>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Sidebar - Profile Card -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md relative">
                    <div class="h-24 bg-gradient-to-r from-purple-600 to-purple-400 rounded-t-lg"></div>
                    <div class="px-6 pb-6">
                        <div class="relative -mt-12 mb-4">
                            <img id="profilePhoto" src="<?php echo $fotoPerfil; ?>" alt="Foto de perfil" class="w-24 h-24 rounded-full border-4 border-white object-cover bg-gray-200">
                            <button onclick="openEditModal()" class="absolute bottom-0 right-0 bg-purple-600 text-white p-2 rounded-full hover:bg-purple-700 transition shadow-lg">
                                <i class="fas fa-camera text-sm"></i>
                            </button>
                        </div>
                        <h2 id="profileName" class="text-xl font-bold text-gray-900"><?php echo $nome; ?></h2>
                        <p id="profileEmail" class="text-gray-600 text-sm mt-1"><?php echo $email; ?></p>
                        <?php if (!empty($cidade) || !empty($estado)): ?>
                            <p class="text-gray-500 text-sm mt-2">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                <span id="profileLocation"><?php echo trim($cidade . ', ' . $estado, ', '); ?></span>
                            </p>
                        <?php endif; ?>
                        
                        <button onclick="openEditModal()" class="w-full mt-4 bg-purple-600 text-white py-2 rounded-lg hover:bg-purple-700 transition">
                            Editar Perfil
                        </button>
                        
                        <!-- Configurações da Conta -->
                        <div class="relative mt-2 z-50">
                            <button onclick="toggleAccountSettings()" class="w-full bg-gray-100 text-gray-700 py-2 rounded-lg hover:bg-gray-200 transition flex items-center justify-center">
                                <i class="fas fa-cog mr-2"></i>Configurações da Conta
                                <i class="fas fa-chevron-down ml-2 text-xs" id="settingsChevron"></i>
                            </button>
                            <div id="accountSettingsMenu" class="hidden absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-lg shadow-xl z-50 overflow-hidden">
                                <button onclick="openChangePasswordModal()" class="w-full text-left px-4 py-3 hover:bg-gray-50 transition flex items-center text-gray-700">
                                    <i class="fas fa-key mr-3 text-purple-600"></i>
                                    Trocar Senha
                                </button>
                                <button onclick="openChangeEmailModal()" class="w-full text-left px-4 py-3 hover:bg-gray-50 transition flex items-center text-gray-700">
                                    <i class="fas fa-envelope mr-3 text-purple-600"></i>
                                    Trocar Email
                                </button>
                                <div class="border-t border-gray-200"></div>
                                <button onclick="openDeleteAccountModal(); toggleAccountSettings();" class="w-full text-left px-4 py-3 hover:bg-red-50 transition flex items-center text-red-600">
                                    <i class="fas fa-trash-alt mr-3"></i>
                                    Excluir Conta
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Skills Card -->
                <div class="bg-white rounded-lg shadow-md p-6 mt-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Habilidades</h3>
                    <div id="skillsList" class="flex flex-wrap gap-2">
                        <?php if (!empty($habilidades)): ?>
                            <?php foreach ($habilidades as $skill): ?>
                                <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-sm"><?php echo htmlspecialchars($skill); ?></span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-gray-500 text-sm">Nenhuma habilidade cadastrada</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- CV Card -->
                <?php if ($curriculoPath): ?>
                    <div class="bg-white rounded-lg shadow-md p-6 mt-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Currículo</h3>
                        <a href="<?php echo htmlspecialchars($curriculoPath); ?>" target="_blank" class="text-purple-600 hover:text-purple-700 flex items-center">
                            <i class="fas fa-file-pdf mr-2"></i>
                            Ver currículo
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- About Section -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-900">Sobre</h3>
                        <button onclick="openEditModal()" class="text-purple-600 hover:text-purple-700">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>
                    <p id="profileAbout" class="text-gray-700 text-sm leading-relaxed">
                        <?php echo !empty($sobre) ? nl2br($sobre) : 'Nenhuma informação sobre você foi adicionada ainda.'; ?>
                    </p>
                </div>

                <!-- Formação Acadêmica -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-900">Formação Acadêmica</h3>
                        <button onclick="openEditModal()" class="text-purple-600 hover:text-purple-700">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>
                    <div id="formacaoList" class="space-y-4">
                        <?php if (!empty($formacao)): ?>
                            <?php foreach ($formacao as $form): ?>
                                <div class="flex space-x-4 border-l-4 border-purple-500 pl-4">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-900"><?php echo htmlspecialchars($form['curso'] ?? ''); ?></h4>
                                        <p class="text-sm text-gray-600"><?php echo htmlspecialchars($form['instituicao'] ?? ''); ?></p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            <?php echo htmlspecialchars($form['periodo'] ?? ''); ?>
                                            <?php if (!empty($form['status'])): ?>
                                                - <?php echo htmlspecialchars($form['status']); ?>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-gray-500 text-sm">Nenhuma formação cadastrada</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Experience Section -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-900">Experiência Profissional</h3>
                        <button onclick="openEditModal()" class="text-purple-600 hover:text-purple-700">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>
                    <div id="experienceList" class="space-y-4">
                        <?php if (!empty($experiencias)): ?>
                            <?php foreach ($experiencias as $exp): ?>
                                <div class="flex space-x-4 border-l-4 border-purple-500 pl-4">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-900"><?php echo htmlspecialchars($exp['cargo'] ?? ''); ?></h4>
                                        <p class="text-sm text-gray-600"><?php echo htmlspecialchars($exp['empresa'] ?? ''); ?></p>
                                        <p class="text-xs text-gray-500 mt-1"><?php echo htmlspecialchars($exp['periodo'] ?? ''); ?></p>
                                        <?php if (!empty($exp['descricao'])): ?>
                                            <p class="text-sm text-gray-700 mt-2"><?php echo nl2br(htmlspecialchars($exp['descricao'])); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-gray-500 text-sm">Nenhuma experiência cadastrada</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Histórico de Candidaturas -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-900">Minhas Candidaturas</h3>
                        <select id="filterCandidaturasStatus" class="border border-gray-300 rounded-lg px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="">Todas</option>
                            <option value="pendente">Pendente</option>
                            <option value="em_analise">Em Análise</option>
                            <option value="aprovada">Aprovada</option>
                            <option value="rejeitada">Reprovada</option>
                        </select>
                    </div>
                    <div id="candidaturasList" class="space-y-4">
                        <p class="text-gray-500 text-center py-8">Carregando candidaturas...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Profile Modal -->
    <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-gray-900">Editar Perfil Completo</h3>
                    <button onclick="closeEditModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <form id="editProfileForm" action="processar_perfil_pcd.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                    <?php
                    // Adicionar token CSRF
                    require_once 'includes/csrf.php';
                    echo csrfField();
                    ?>
                    
                    <!-- Informações Básicas -->
                    <div class="border-b pb-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Informações Básicas</h4>
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nome Completo *</label>
                                <input type="text" name="nome" id="editName" value="<?php echo $nome; ?>" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                                <input type="email" name="email" id="editEmail" value="<?php echo $email; ?>" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Telefone</label>
                                <input type="text" name="telefone" id="editTelefone" value="<?php echo $telefone; ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Foto de Perfil</label>
                                <input type="file" name="foto_perfil" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                                <p class="text-xs text-gray-500 mt-1">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Formatos aceitos: JPG, PNG, GIF, WebP. 
                                    Tamanho máximo: 5MB. 
                                    Dimensão mínima: 100x100px. 
                                    Dimensão máxima: 5000x5000px. 
                                    Dimensão ideal: 400x400px
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Endereço -->
                    <div class="border-b pb-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Endereço</h4>
                        <div class="grid md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">CEP</label>
                                <input type="text" name="cep" id="editCep" value="<?php echo htmlspecialchars($userData['cep'] ?? ''); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                                <input type="text" name="estado" id="editEstado" value="<?php echo $estado; ?>" maxlength="2" placeholder="SP" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Cidade</label>
                                <input type="text" name="cidade" id="editCidade" value="<?php echo $cidade; ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Logradouro</label>
                                <input type="text" name="logradouro" id="editLogradouro" value="<?php echo htmlspecialchars($userData['logradouro'] ?? ''); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Número</label>
                                <input type="text" name="numero" id="editNumero" value="<?php echo htmlspecialchars($userData['numero'] ?? ''); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Bairro</label>
                                <input type="text" name="bairro" id="editBairro" value="<?php echo htmlspecialchars($userData['bairro'] ?? ''); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Complemento</label>
                                <input type="text" name="complemento" id="editComplemento" value="<?php echo htmlspecialchars($userData['complemento'] ?? ''); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                            </div>
                        </div>
                    </div>

                    <!-- Sobre -->
                    <div class="border-b pb-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Sobre</h4>
                        <textarea name="sobre" id="editSobre" rows="5" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent" placeholder="Conte um pouco sobre você, suas experiências e objetivos profissionais..."><?php echo $sobre; ?></textarea>
                    </div>

                    <!-- Habilidades -->
                    <div class="border-b pb-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Habilidades</h4>
                        <div id="habilidadesContainer" class="space-y-2">
                            <?php if (!empty($habilidades)): ?>
                                <?php foreach ($habilidades as $index => $skill): ?>
                                    <div class="flex gap-2 items-center habilidade-item">
                                        <input type="text" name="habilidades[]" value="<?php echo htmlspecialchars($skill); ?>" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent" placeholder="Ex: JavaScript, React, PHP...">
                                        <button type="button" onclick="removeSkill(this)" class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <button type="button" onclick="addSkill()" class="mt-2 text-purple-600 hover:text-purple-700 text-sm">
                            <i class="fas fa-plus mr-1"></i> Adicionar habilidade
                        </button>
                    </div>

                    <!-- Formação Acadêmica -->
                    <div class="border-b pb-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Formação Acadêmica</h4>
                        <div id="formacaoContainer" class="space-y-4">
                            <?php if (!empty($formacao)): ?>
                                <?php foreach ($formacao as $index => $form): ?>
                                    <div class="border p-4 rounded-lg formacao-item">
                                        <div class="grid md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Curso *</label>
                                                <input type="text" name="formacao[<?php echo $index; ?>][curso]" value="<?php echo htmlspecialchars($form['curso'] ?? ''); ?>" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Instituição *</label>
                                                <input type="text" name="formacao[<?php echo $index; ?>][instituicao]" value="<?php echo htmlspecialchars($form['instituicao'] ?? ''); ?>" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Período</label>
                                                <input type="text" name="formacao[<?php echo $index; ?>][periodo]" value="<?php echo htmlspecialchars($form['periodo'] ?? ''); ?>" placeholder="Ex: 2018 - 2022" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                                <select name="formacao[<?php echo $index; ?>][status]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                                                    <option value="">Selecione...</option>
                                                    <option value="Concluído" <?php echo (($form['status'] ?? '') === 'Concluído') ? 'selected' : ''; ?>>Concluído</option>
                                                    <option value="Em andamento" <?php echo (($form['status'] ?? '') === 'Em andamento') ? 'selected' : ''; ?>>Em andamento</option>
                                                    <option value="Trancado" <?php echo (($form['status'] ?? '') === 'Trancado') ? 'selected' : ''; ?>>Trancado</option>
                                                </select>
                                            </div>
                                        </div>
                                        <button type="button" onclick="removeFormacao(this)" class="mt-2 text-red-600 hover:text-red-700 text-sm">
                                            <i class="fas fa-trash mr-1"></i> Remover formação
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <button type="button" onclick="addFormacao()" class="mt-2 text-purple-600 hover:text-purple-700 text-sm">
                            <i class="fas fa-plus mr-1"></i> Adicionar formação
                        </button>
                    </div>

                    <!-- Experiências -->
                    <div class="border-b pb-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Experiência Profissional</h4>
                        <div id="experienciasContainer" class="space-y-4">
                            <?php if (!empty($experiencias)): ?>
                                <?php foreach ($experiencias as $index => $exp): ?>
                                    <div class="border p-4 rounded-lg experiencia-item">
                                        <div class="grid md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Cargo *</label>
                                                <input type="text" name="experiencias[<?php echo $index; ?>][cargo]" value="<?php echo htmlspecialchars($exp['cargo'] ?? ''); ?>" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Empresa *</label>
                                                <input type="text" name="experiencias[<?php echo $index; ?>][empresa]" value="<?php echo htmlspecialchars($exp['empresa'] ?? ''); ?>" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Período</label>
                                                <input type="text" name="experiencias[<?php echo $index; ?>][periodo]" value="<?php echo htmlspecialchars($exp['periodo'] ?? ''); ?>" placeholder="Ex: Jan 2021 - Presente" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Descrição</label>
                                                <textarea name="experiencias[<?php echo $index; ?>][descricao]" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent"><?php echo htmlspecialchars($exp['descricao'] ?? ''); ?></textarea>
                                            </div>
                                        </div>
                                        <button type="button" onclick="removeExperiencia(this)" class="mt-2 text-red-600 hover:text-red-700 text-sm">
                                            <i class="fas fa-trash mr-1"></i> Remover experiência
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <button type="button" onclick="addExperiencia()" class="mt-2 text-purple-600 hover:text-purple-700 text-sm">
                            <i class="fas fa-plus mr-1"></i> Adicionar experiência
                        </button>
                    </div>

                    <!-- Currículo -->
                    <div class="border-b pb-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Currículo</h4>
                        <?php if ($curriculoPath): ?>
                            <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                                <p class="text-sm text-gray-700 mb-2">Currículo atual:</p>
                                <a href="<?php echo htmlspecialchars($curriculoPath); ?>" target="_blank" class="text-purple-600 hover:text-purple-700">
                                    <i class="fas fa-file-pdf mr-2"></i>Ver currículo atual
                                </a>
                            </div>
                        <?php endif; ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Enviar novo currículo (PDF)</label>
                            <input type="file" name="curriculo" accept="application/pdf" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">Formato aceito: PDF. Tamanho máximo: 10MB</p>
                        </div>
                    </div>

                    <!-- Alterar Senha (Opcional) -->
                    <div class="pb-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Alterar Senha (Opcional)</h4>
                        <div class="grid md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Senha Atual</label>
                                <input type="password" name="senha_atual" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nova Senha</label>
                                <input type="password" name="nova_senha" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Confirmar Nova Senha</label>
                                <input type="password" name="confirmar_senha" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Deixe em branco se não quiser alterar a senha</p>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t">
                        <button type="button" onclick="closeEditModal()" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                            Cancelar
                        </button>
                        <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                            Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<<<<<<< HEAD
    <!-- Delete Account Modal -->
    <div id="deleteAccountModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg max-w-md w-full">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-red-600">Excluir Conta</h3>
                    <button onclick="closeDeleteAccountModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <div class="mb-6">
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                        <p class="text-sm text-red-800 font-semibold mb-2">
                            <i class="fas fa-exclamation-triangle mr-2"></i>Atenção: Esta ação é irreversível!
                        </p>
                        <p class="text-sm text-red-700">
                            Ao excluir sua conta, todos os seus dados serão permanentemente removidos, incluindo:
                        </p>
                        <ul class="list-disc list-inside text-sm text-red-700 mt-2 space-y-1">
                            <li>Seu perfil completo</li>
                            <li>Todas as suas candidaturas</li>
                            <li>Seus arquivos (foto, currículo, laudo médico)</li>
                            <li>Histórico de atividades</li>
                        </ul>
                    </div>
                    
                    <p class="text-sm text-gray-700 mb-4">
                        Para confirmar a exclusão, digite <strong class="text-red-600">EXCLUIR</strong> no campo abaixo:
                    </p>
                    
                    <input 
                        type="text" 
                        id="confirmDeleteInput" 
                        placeholder="Digite EXCLUIR para confirmar"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-600 focus:border-transparent"
                        autocomplete="off"
                    >
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button 
                        onclick="closeDeleteAccountModal()" 
                        class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition"
                    >
                        Cancelar
                    </button>
                    <button 
                        id="confirmDeleteButton"
                        onclick="confirmDeleteAccount()" 
                        class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition"
                    >
                        <i class="fas fa-trash-alt mr-2"></i>Excluir Conta
                    </button>
                </div>
            </div>
        </div>
    </div>

<?php
// Incluir footer escuro (para páginas de perfil)
$footerStyle = 'dark';
include 'includes/footer.php';
?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script>
        // Funções para gerenciar habilidades
        function addSkill() {
            const container = document.getElementById('habilidadesContainer');
            const div = document.createElement('div');
            div.className = 'flex gap-2 items-center habilidade-item';
            div.innerHTML = `
                <input type="text" name="habilidades[]" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent" placeholder="Ex: JavaScript, React, PHP...">
                <button type="button" onclick="removeSkill(this)" class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                    <i class="fas fa-times"></i>
                </button>
            `;
            container.appendChild(div);
        }

        function removeSkill(button) {
            button.closest('.habilidade-item').remove();
        }

        // Funções para gerenciar formação
        let formacaoIndex = <?php echo !empty($formacao) ? count($formacao) : 0; ?>;
        function addFormacao() {
            const container = document.getElementById('formacaoContainer');
            const div = document.createElement('div');
            div.className = 'border p-4 rounded-lg formacao-item';
            div.innerHTML = `
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Curso *</label>
                        <input type="text" name="formacao[${formacaoIndex}][curso]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Instituição *</label>
                        <input type="text" name="formacao[${formacaoIndex}][instituicao]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Período</label>
                        <input type="text" name="formacao[${formacaoIndex}][periodo]" placeholder="Ex: 2018 - 2022" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="formacao[${formacaoIndex}][status]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                            <option value="">Selecione...</option>
                            <option value="Concluído">Concluído</option>
                            <option value="Em andamento">Em andamento</option>
                            <option value="Trancado">Trancado</option>
                        </select>
                    </div>
                </div>
                <button type="button" onclick="removeFormacao(this)" class="mt-2 text-red-600 hover:text-red-700 text-sm">
                    <i class="fas fa-trash mr-1"></i> Remover formação
                </button>
            `;
            container.appendChild(div);
            formacaoIndex++;
        }

        function removeFormacao(button) {
            button.closest('.formacao-item').remove();
        }

        // Funções para gerenciar experiências
        let experienciaIndex = <?php echo !empty($experiencias) ? count($experiencias) : 0; ?>;
        function addExperiencia() {
            const container = document.getElementById('experienciasContainer');
            const div = document.createElement('div');
            div.className = 'border p-4 rounded-lg experiencia-item';
            div.innerHTML = `
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cargo *</label>
                        <input type="text" name="experiencias[${experienciaIndex}][cargo]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Empresa *</label>
                        <input type="text" name="experiencias[${experienciaIndex}][empresa]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Período</label>
                        <input type="text" name="experiencias[${experienciaIndex}][periodo]" placeholder="Ex: Jan 2021 - Presente" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Descrição</label>
                        <textarea name="experiencias[${experienciaIndex}][descricao]" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent"></textarea>
                    </div>
                </div>
                <button type="button" onclick="removeExperiencia(this)" class="mt-2 text-red-600 hover:text-red-700 text-sm">
                    <i class="fas fa-trash mr-1"></i> Remover experiência
                </button>
            `;
            container.appendChild(div);
            experienciaIndex++;
        }

        function removeExperiencia(button) {
            button.closest('.experiencia-item').remove();
        }

        // Funções do modal
        function openEditModal() {
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        // Preview e validação de foto antes de enviar
        document.querySelector('input[name="foto_perfil"]')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            const fileInput = e.target;
            const errorMsg = fileInput.parentElement.querySelector('.file-error');
            
            // Remover mensagem de erro anterior
            if (errorMsg) {
                errorMsg.remove();
            }
            
            if (!file) {
                return;
            }
            
            // Validar tipo de arquivo
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                const errorDiv = document.createElement('p');
                errorDiv.className = 'file-error text-red-600 text-xs mt-1';
                errorDiv.textContent = 'Formato não permitido. Use apenas JPG, PNG, GIF ou WebP.';
                fileInput.parentElement.appendChild(errorDiv);
                fileInput.value = '';
                return;
            }
            
            // Validar tamanho (máximo 5MB)
            const maxSize = 5 * 1024 * 1024; // 5MB
            if (file.size > maxSize) {
                const errorDiv = document.createElement('p');
                errorDiv.className = 'file-error text-red-600 text-xs mt-1';
                errorDiv.textContent = 'Arquivo muito grande. Tamanho máximo: 5MB.';
                fileInput.parentElement.appendChild(errorDiv);
                fileInput.value = '';
                return;
            }
            
            // Validar dimensões usando FileReader e Image
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = new Image();
                img.onload = function() {
                    const minWidth = 100;
                    const minHeight = 100;
                    const maxWidth = 5000;
                    const maxHeight = 5000;
                    
                    if (img.width < minWidth || img.height < minHeight) {
                        const errorDiv = document.createElement('p');
                        errorDiv.className = 'file-error text-red-600 text-xs mt-1';
                        errorDiv.textContent = `Imagem muito pequena. Dimensão mínima: ${minWidth}x${minHeight}px.`;
                        fileInput.parentElement.appendChild(errorDiv);
                        fileInput.value = '';
                        return;
                    }
                    
                    if (img.width > maxWidth || img.height > maxHeight) {
                        const errorDiv = document.createElement('p');
                        errorDiv.className = 'file-error text-red-600 text-xs mt-1';
                        errorDiv.textContent = `Imagem muito grande. Dimensão máxima: ${maxWidth}x${maxHeight}px.`;
                        fileInput.parentElement.appendChild(errorDiv);
                        fileInput.value = '';
                        return;
                    }
                    
                    // Se passou todas as validações, mostrar preview
                    const profilePhoto = document.getElementById('profilePhoto');
                    if (profilePhoto) {
                        profilePhoto.src = e.target.result;
                    }
                    
                    // Mostrar mensagem de sucesso
                    const successDiv = document.createElement('p');
                    successDiv.className = 'file-success text-green-600 text-xs mt-1';
                    successDiv.innerHTML = `<i class="fas fa-check-circle mr-1"></i>Imagem válida (${img.width}x${img.height}px, ${(file.size / 1024).toFixed(2)}KB)`;
                    fileInput.parentElement.appendChild(successDiv);
                    
                    // Remover mensagem de sucesso após 3 segundos
                    setTimeout(() => {
                        if (successDiv.parentElement) {
                            successDiv.remove();
                        }
                    }, 3000);
                };
                img.onerror = function() {
                    const errorDiv = document.createElement('p');
                    errorDiv.className = 'file-error text-red-600 text-xs mt-1';
                    errorDiv.textContent = 'Arquivo não é uma imagem válida.';
                    fileInput.parentElement.appendChild(errorDiv);
                    fileInput.value = '';
                };
                img.src = e.target.result;
            };
            reader.onerror = function() {
                const errorDiv = document.createElement('p');
                errorDiv.className = 'file-error text-red-600 text-xs mt-1';
                errorDiv.textContent = 'Erro ao ler o arquivo.';
                fileInput.parentElement.appendChild(errorDiv);
                fileInput.value = '';
            };
            reader.readAsDataURL(file);
        });

        // Máscaras e busca automática de CEP
        let cepTimeout;
        document.getElementById('editCep')?.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 8) {
                value = value.replace(/(\d{5})(\d)/, '$1-$2');
                e.target.value = value;
                
                // Buscar CEP automaticamente quando tiver 8 dígitos
                if (value.replace(/\D/g, '').length === 8) {
                    // Limpar timeout anterior
                    clearTimeout(cepTimeout);
                    
                    // Aguardar 500ms após parar de digitar
                    cepTimeout = setTimeout(async () => {
                        const cep = value.replace(/\D/g, '');
                        
                        // Mostrar indicador de carregamento
                        const cepInput = e.target;
                        cepInput.disabled = true;
                        cepInput.style.opacity = '0.6';
                        
                        // Adicionar ícone de loading se não existir
                        let loadingIcon = cepInput.parentElement.querySelector('.cep-loading');
                        if (!loadingIcon) {
                            loadingIcon = document.createElement('i');
                            loadingIcon.className = 'fas fa-spinner fa-spin cep-loading';
                            loadingIcon.style.position = 'absolute';
                            loadingIcon.style.right = '10px';
                            loadingIcon.style.top = '50%';
                            loadingIcon.style.transform = 'translateY(-50%)';
                            loadingIcon.style.color = '#9333ea';
                            cepInput.parentElement.style.position = 'relative';
                            cepInput.parentElement.appendChild(loadingIcon);
                        }
                        
                        try {
                            // Usar a função do api.js se disponível, senão fazer fetch direto
                            let result;
                            if (window.ViggedAPI && window.ViggedAPI.buscarCep) {
                                result = await window.ViggedAPI.buscarCep(cep);
                            } else {
                                const response = await fetch(`api/buscar_cep.php?cep=${cep}`);
                                result = await response.json();
                            }
                            
                            if (result.success && result.data) {
                                // Preencher campos automaticamente
                                const data = result.data;
                                
                                if (data.logradouro) {
                                    document.getElementById('editLogradouro').value = data.logradouro;
                                }
                                if (data.bairro) {
                                    document.getElementById('editBairro').value = data.bairro;
                                }
                                if (data.cidade) {
                                    document.getElementById('editCidade').value = data.cidade;
                                }
                                if (data.estado) {
                                    document.getElementById('editEstado').value = data.estado.toUpperCase();
                                }
                                if (data.complemento) {
                                    document.getElementById('editComplemento').value = data.complemento;
                                }
                                
                                // Focar no campo número após preencher
                                document.getElementById('editNumero').focus();
                                
                                // Mostrar mensagem de sucesso temporária
                                showCepMessage('CEP encontrado! Endereço preenchido automaticamente.', 'success');
                            } else {
                                showCepMessage(result.error || 'CEP não encontrado', 'error');
                            }
                        } catch (error) {
                            console.error('Erro ao buscar CEP:', error);
                            showCepMessage('Erro ao buscar CEP. Tente novamente.', 'error');
                        } finally {
                            // Remover indicador de carregamento
                            cepInput.disabled = false;
                            cepInput.style.opacity = '1';
                            if (loadingIcon) {
                                loadingIcon.remove();
                            }
                        }
                    }, 500);
                }
            }
        });
        
        // Função para mostrar mensagem de CEP
        function showCepMessage(message, type) {
            // Remover mensagem anterior se existir
            const existingMsg = document.querySelector('.cep-message');
            if (existingMsg) {
                existingMsg.remove();
            }
            
            // Criar nova mensagem
            const msgDiv = document.createElement('div');
            msgDiv.className = `cep-message mt-2 p-2 rounded text-sm ${
                type === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
            }`;
            msgDiv.textContent = message;
            
            // Inserir após o campo CEP
            const cepInput = document.getElementById('editCep');
            if (cepInput && cepInput.parentElement) {
                cepInput.parentElement.appendChild(msgDiv);
                
                // Remover mensagem após 3 segundos
                setTimeout(() => {
                    msgDiv.remove();
                }, 3000);
            }
        }

        document.getElementById('editTelefone')?.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 11) {
                if (value.length > 6) {
                    value = value.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
                } else if (value.length > 2) {
                    value = value.replace(/(\d{2})(\d{0,5})/, '($1) $2');
                }
                e.target.value = value;
            }
        });

        // Fechar modal ao clicar fora
        document.getElementById('editModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });
        
        // Carregar histórico de candidaturas
        async function loadCandidaturas() {
            const status = document.getElementById('filterCandidaturasStatus')?.value || '';
            const params = status ? `?status=${status}` : '';
            
            try {
                const response = await fetch(`api/historico_candidaturas.php${params}`);
                const result = await response.json();
                
                if (result.success) {
                    displayCandidaturas(result.data);
                } else {
                    document.getElementById('candidaturasList').innerHTML = `<p class="text-red-500 text-center py-8">${result.error || 'Erro ao carregar candidaturas'}</p>`;
                }
            } catch (error) {
                console.error('Erro ao carregar candidaturas:', error);
                document.getElementById('candidaturasList').innerHTML = '<p class="text-red-500 text-center py-8">Erro ao carregar candidaturas.</p>';
            }
        }
        
        function displayCandidaturas(candidaturas) {
            const candidaturasList = document.getElementById('candidaturasList');
            
            if (candidaturas.length === 0) {
                candidaturasList.innerHTML = '<p class="text-gray-500 text-center py-8">Você ainda não se candidatou a nenhuma vaga.</p>';
                return;
            }
            
            const statusColors = {
                'pendente': 'bg-yellow-100 text-yellow-800',
                'em_analise': 'bg-blue-100 text-blue-800',
                'aprovada': 'bg-green-100 text-green-800',
                'rejeitada': 'bg-red-100 text-red-800',
                'cancelada': 'bg-gray-100 text-gray-800'
            };
            
            candidaturasList.innerHTML = candidaturas.map(candidatura => {
                const statusColor = statusColors[candidatura.status] || 'bg-gray-100 text-gray-800';
                const createdDate = new Date(candidatura.created_at);
                const dateStr = createdDate.toLocaleDateString('pt-BR');
                
                let starsHtml = '';
                if (candidatura.avaliacao) {
                    starsHtml = '<div class="flex items-center mt-2">';
                    for (let i = 1; i <= 5; i++) {
                        starsHtml += `<i class="fas fa-star ${i <= candidatura.avaliacao ? 'text-yellow-400' : 'text-gray-300'}"></i>`;
                    }
                    starsHtml += '</div>';
                }
                
                return `
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    ${candidatura.empresa_logo ? 
                                        `<img src="${candidatura.empresa_logo}" alt="${candidatura.empresa_nome}" class="w-12 h-12 rounded object-cover">` :
                                        `<div class="w-12 h-12 rounded bg-purple-100 flex items-center justify-center">
                                            <i class="fas fa-building text-purple-600"></i>
                                        </div>`
                                    }
                                    <div>
                                        <h4 class="font-semibold text-gray-900">${candidatura.vaga_titulo || 'Sem título'}</h4>
                                        <p class="text-sm text-gray-600">${candidatura.empresa_nome || candidatura.empresa_fantasia || 'Empresa'}</p>
                                    </div>
                                </div>
                                <div class="ml-15 space-y-1">
                                    <p class="text-sm text-gray-600">
                                        <i class="fas fa-calendar mr-1"></i>Candidatou-se em ${dateStr}
                                    </p>
                                    ${candidatura.localizacao ? 
                                        `<p class="text-sm text-gray-600">
                                            <i class="fas fa-map-marker-alt mr-1"></i>${candidatura.localizacao}
                                        </p>` : ''
                                    }
                                    ${candidatura.tipo_contrato ? 
                                        `<p class="text-sm text-gray-600">
                                            <i class="fas fa-briefcase mr-1"></i>${candidatura.tipo_contrato}
                                        </p>` : ''
                                    }
                                    ${candidatura.faixa_salarial ? 
                                        `<p class="text-sm text-gray-600">
                                            <i class="fas fa-dollar-sign mr-1"></i>${candidatura.faixa_salarial}
                                        </p>` : ''
                                    }
                                    ${candidatura.mensagem ? 
                                        `<p class="text-sm text-gray-700 mt-2 p-2 bg-gray-50 rounded">
                                            <strong>Sua mensagem:</strong> ${candidatura.mensagem.substring(0, 150)}${candidatura.mensagem.length > 150 ? '...' : ''}
                                        </p>` : ''
                                    }
                                    ${candidatura.feedback ? 
                                        `<p class="text-sm text-red-700 mt-2 p-2 bg-red-50 rounded">
                                            <strong>Feedback da empresa:</strong> ${candidatura.feedback}
                                        </p>` : ''
                                    }
                                    ${starsHtml}
                                </div>
                            </div>
                            <div class="ml-4 flex flex-col items-end gap-2">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold ${statusColor}">
                                    ${candidatura.status_label || candidatura.status}
                                </span>
                                <a href="detalhes-vaga.php?id=${candidatura.job_id}" class="text-purple-600 hover:text-purple-700 text-sm mt-2">
                                    Ver vaga <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }
        
        // Carregar candidaturas ao carregar a página
        document.addEventListener('DOMContentLoaded', function() {
            loadCandidaturas();
            
            const filterStatus = document.getElementById('filterCandidaturasStatus');
            if (filterStatus) {
                filterStatus.addEventListener('change', loadCandidaturas);
            }
        });
        
        // Funções para menu de configurações
        function toggleAccountSettings() {
            const menu = document.getElementById('accountSettingsMenu');
            const chevron = document.getElementById('settingsChevron');
            menu.classList.toggle('hidden');
            chevron.classList.toggle('fa-chevron-down');
            chevron.classList.toggle('fa-chevron-up');
        }
        
        // Fechar menu ao clicar fora
        document.addEventListener('click', function(event) {
            const settingsButton = event.target.closest('[onclick="toggleAccountSettings()"]');
            const settingsMenu = document.getElementById('accountSettingsMenu');
            if (settingsMenu && !settingsButton && !settingsMenu.contains(event.target)) {
                settingsMenu.classList.add('hidden');
                const chevron = document.getElementById('settingsChevron');
                if (chevron) {
                    chevron.classList.remove('fa-chevron-up');
                    chevron.classList.add('fa-chevron-down');
                }
            }
        });
        
        // Funções para modais de configurações
        function openChangePasswordModal() {
            alert('Funcionalidade de trocar senha será implementada em breve.');
            toggleAccountSettings();
        }
        
        function openChangeEmailModal() {
            alert('Funcionalidade de trocar email será implementada em breve.');
            toggleAccountSettings();
        }
        
        // Funções para excluir conta
        function openDeleteAccountModal() {
            document.getElementById('deleteAccountModal').classList.remove('hidden');
        }
        
        function closeDeleteAccountModal() {
            document.getElementById('deleteAccountModal').classList.add('hidden');
        }
        
        async function confirmDeleteAccount() {
            const confirmInput = document.getElementById('confirmDeleteInput');
            if (confirmInput.value.toLowerCase() !== 'excluir') {
                alert('Por favor, digite "EXCLUIR" para confirmar a exclusão da conta.');
                return;
            }
            
            const deleteButton = document.getElementById('confirmDeleteButton');
            const originalText = deleteButton.innerHTML;
            deleteButton.disabled = true;
            deleteButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Excluindo...';
            
            try {
                const result = await ViggedAPI.excluirConta();
                
                if (result.success) {
                    alert('Sua conta foi excluída com sucesso. Você será redirecionado para a página inicial.');
                    window.location.href = 'index.php';
                } else {
                    alert('Erro ao excluir conta: ' + (result.error || 'Erro desconhecido'));
                    deleteButton.disabled = false;
                    deleteButton.innerHTML = originalText;
                }
            } catch (error) {
                console.error('Erro ao excluir conta:', error);
                alert('Erro ao excluir conta. Tente novamente mais tarde.');
                deleteButton.disabled = false;
                deleteButton.innerHTML = originalText;
            }
        }
    </script>
