import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import api from '@/plugins/axios';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: '127.0.0.1',
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
    authorizer: (channel, options) => {
        return {
            authorize: (socketId, callback) => {
                api.post('/broadcasting/auth', {
                    socket_id: socketId,
                    channel_name: channel.name
                })
                .then(response => {
                    callback(false, response.data);
                })
                .catch(error => {
                    console.error(`🚨 Broadcast Auth Failed [${channel.name}]:`, error);
                    callback(true, error);
                });
            }
        };
    },
});

// ─── Connection & Socket ID Sync ───
if (window.Echo.connector.pusher) {
    window.Echo.connector.pusher.connection.bind('connected', () => {
        const socketId = window.Echo.socketId();
        // Inject socket ID into axios for 'toOthers()' support
        api.defaults.headers.common['X-Socket-ID'] = socketId;
        console.log(`%c 🆔 SOCKET ID: ${socketId} `, 'background: #222; color: #fff; padding: 2px; border-radius: 4px;');
    });

    window.Echo.connector.pusher.connection.bind('state_change', (states) => {
        const icon = {
            connecting: '⏳',
            connected: '✅',
            unavailable: '❌',
            failed: '🚨',
            disconnected: '🔌'
        }[states.current] || '📡';

        console.log(`%c ${icon} Reverb System: ${states.current.toUpperCase()} `, 'background: #000; color: #00ff00; font-weight: bold; padding: 4px; border-radius: 4px;');
    });

    window.Echo.connector.pusher.connection.bind('error', (err) => {
        console.error('❌ Reverb Connection Error:', err);
    });
}
