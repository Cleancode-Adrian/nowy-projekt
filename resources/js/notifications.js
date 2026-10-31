/**
 * Most między prywatnym kanałem Echo/Reverb a resztą strony.
 *
 * Subskrybujemy kanał "notifications.{id}" tylko raz, tutaj, i rozgłaszamy
 * dalej przez zwykły EventTarget - tak komponenty Alpine w headerze (dzwonek,
 * ikona wiadomości) nie muszą same nic wiedzieć o Echo/Pusher/Reverb.
 */
window.liveNotifications = new EventTarget();

document.addEventListener('DOMContentLoaded', () => {
    const userId = document.querySelector('meta[name="auth-user-id"]')?.content;

    if (!userId || !window.Echo) {
        return;
    }

    window.Echo.private(`notifications.${userId}`)
        .listen('.notification.created', (payload) => {
            window.dispatchEvent(new CustomEvent('notify', {
                detail: { message: payload.title, type: 'info' },
            }));

            window.liveNotifications.dispatchEvent(new CustomEvent('notification', { detail: payload }));
            window.liveNotifications.dispatchEvent(new CustomEvent(payload.type, { detail: payload }));
        });
});
