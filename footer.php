<?php
/**
 * Footer comum para todas as páginas
 * Vigged - Plataforma de Inclusão e Oportunidades
 * 
 * @param string $footerStyle Estilo do footer: 'purple' (padrão) ou 'dark'
 */
$footerStyle = isset($footerStyle) ? $footerStyle : 'purple';
$bgClass = $footerStyle === 'dark' ? 'bg-gray-900' : 'bg-purple-600';
$textClass = $footerStyle === 'dark' ? 'text-white' : 'text-white';
$linkClass = $footerStyle === 'dark' ? 'text-gray-400 hover:text-white' : 'text-purple-200 hover:text-white';
$borderClass = $footerStyle === 'dark' ? 'border-gray-800' : 'border-purple-500';
$copyrightClass = $footerStyle === 'dark' ? 'text-gray-400' : 'text-purple-200';
?>
<!-- Footer -->
<footer class="<?php echo $bgClass; ?> <?php echo $textClass; ?> py-12">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid md:grid-cols-4 gap-8 mb-8">
            <div>
                <h3 class="text-2xl font-bold mb-4">Vigged</h3>
                <p class="<?php echo $linkClass; ?> mb-4">
                    Conectando talentos PCD às melhores oportunidades do mercado.
                </p>
                <div class="flex space-x-4">
                    <a href="#" class="hover:<?php echo $linkClass; ?> transition" aria-label="Facebook">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    <a href="#" class="hover:<?php echo $linkClass; ?> transition" aria-label="Twitter">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                    </a>
                    <a href="#" class="hover:<?php echo $linkClass; ?> transition" aria-label="Instagram">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/></svg>
                    </a>
                    <a href="#" class="hover:<?php echo $linkClass; ?> transition" aria-label="LinkedIn">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                    </a>
                </div>
            </div>
            <div>
                <h4 class="font-semibold mb-4">Para Candidatos</h4>
                <ul class="space-y-2 <?php echo $linkClass; ?>">
                    <li><a href="vagas.php" class="hover:text-white transition">Buscar Vagas</a></li>
                    <li><a href="pre-cadastro.php" class="hover:text-white transition">Criar Conta</a></li>
                    <li><a href="perfil-pcd.php" class="hover:text-white transition">Meu Perfil</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-4">Para Empresas</h4>
                <ul class="space-y-2 <?php echo $linkClass; ?>">
                    <li><a href="empresas.php" class="hover:text-white transition">Planos</a></li>
                    <li><a href="cadastro-empresa.php" class="hover:text-white transition">Cadastrar Empresa</a></li>
                    <li><a href="perfil-empresa.php" class="hover:text-white transition">Painel Empresa</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-4">Suporte</h4>
                <ul class="space-y-2 <?php echo $linkClass; ?>">
                    <li><a href="suporte.php" class="hover:text-white transition">Contato</a></li>
                    <li><a href="sobre-nos.php" class="hover:text-white transition">Sobre Nós</a></li>
                    <li><a href="#" class="hover:text-white transition">FAQ</a></li>
                    <li><a href="termos.php" class="hover:text-white transition">Termos de Uso</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t <?php echo $borderClass; ?> pt-8 text-center <?php echo $copyrightClass; ?>">
            <p>&copy; <?php echo date('Y'); ?> Vigged. Todos os direitos reservados.</p>
        </div>
    </div>
</footer>

<script>
// Sistema de Notificações
let notificationsPanelOpen = false;

function toggleNotifications() {
    const panel = document.getElementById('notificationsPanel');
    if (panel) {
        notificationsPanelOpen = !notificationsPanelOpen;
        if (notificationsPanelOpen) {
            panel.classList.remove('hidden');
            loadNotifications();
        } else {
            panel.classList.add('hidden');
        }
    }
}

async function loadNotifications() {
    try {
        const response = await fetch('api/notificacoes.php?action=listar&limit=10');
        const result = await response.json();
        
        if (result.success) {
            displayNotifications(result.data);
            updateNotificationBadge(result.nao_lidas);
        }
    } catch (error) {
        console.error('Erro ao carregar notificações:', error);
    }
}

function displayNotifications(notificacoes) {
    const list = document.getElementById('notificationsList');
    
    if (notificacoes.length === 0) {
        list.innerHTML = '<p class="p-4 text-gray-500 text-center">Nenhuma notificação</p>';
        return;
    }
    
    list.innerHTML = notificacoes.map(notif => {
        const date = new Date(notif.created_at);
        const dateStr = date.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
        const unreadClass = notif.lida ? '' : 'bg-purple-50';
        
        return `
            <div class="p-4 hover:bg-gray-50 transition ${unreadClass}">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900 text-sm">${notif.titulo}</h4>
                        <p class="text-gray-600 text-sm mt-1">${notif.mensagem}</p>
                        <p class="text-gray-400 text-xs mt-2">${dateStr}</p>
                    </div>
                    ${!notif.lida ? '<span class="w-2 h-2 bg-purple-600 rounded-full mt-1"></span>' : ''}
                </div>
                ${notif.link ? `<a href="${notif.link}" onclick="markNotificationRead(${notif.id})" class="text-purple-600 text-sm mt-2 inline-block">Ver detalhes →</a>` : ''}
            </div>
        `;
    }).join('');
}

function updateNotificationBadge(count) {
    const badge = document.getElementById('notificationBadge');
    if (badge) {
        if (count > 0) {
            badge.classList.remove('hidden');
            badge.textContent = count > 99 ? '99+' : count;
        } else {
            badge.classList.add('hidden');
        }
    }
}

async function markNotificationRead(notificationId) {
    try {
        const formData = new FormData();
        formData.append('action', 'marcar_lida');
        formData.append('notification_id', notificationId);
        
        await fetch('api/notificacoes.php', {
            method: 'POST',
            body: formData
        });
        
        loadNotifications();
    } catch (error) {
        console.error('Erro ao marcar notificação como lida:', error);
    }
}

async function markAllNotificationsRead() {
    try {
        await fetch('api/notificacoes.php?action=marcar_todas_lidas', { method: 'POST' });
        loadNotifications();
    } catch (error) {
        console.error('Erro ao marcar todas como lidas:', error);
    }
}

// Carregar contador de notificações ao carregar a página
document.addEventListener('DOMContentLoaded', function() {
    loadNotifications();
    // Atualizar a cada 30 segundos
    setInterval(loadNotifications, 30000);
    
    // Fechar painel ao clicar fora
    document.addEventListener('click', function(e) {
        const panel = document.getElementById('notificationsPanel');
        const button = e.target.closest('button[onclick="toggleNotifications()"]');
        if (panel && !panel.contains(e.target) && !button && notificationsPanelOpen) {
            toggleNotifications();
        }
    });
});
</script>

</body>
</html>

