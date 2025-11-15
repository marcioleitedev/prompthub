// frontend/src/echo.js

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

// Função para inicializar o Echo (só quando houver token)
export function initializeEcho() {
    // Obtém o token JWT do armazenamento local
    const userToken = localStorage.getItem('token'); 
    
    if (!userToken) {
        console.log('Echo não inicializado: token não encontrado');
        return null;
    }

    if (window.Echo) {
        console.log('Echo já inicializado');
        return window.Echo;
    }

    try {
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: 'key_portfolio_ia',
            
            // Configurações do servidor WebSocket (Reverb)
            wsHost: 'dflix-prompthub-backend.a8zera.easypanel.host',
            wsPort: 8080,
            wssPort: 8080,
            forceTLS: true,
            disableStats: true,
            
            // Configurações de Autorização
            auth: {
                headers: {
                    'Authorization': `Bearer ${userToken}`,
                },
            },
            authEndpoint: 'https://dflix-prompthub-backend.a8zera.easypanel.host/api/broadcasting/auth', 
        });

        console.log('Echo inicializado com sucesso');
        return window.Echo;
    } catch (error) {
        console.error('Erro ao inicializar Echo:', error);
        return null;
    }
}

// Não inicializa automaticamente no carregamento
export default { initializeEcho };