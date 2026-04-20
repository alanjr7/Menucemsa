/**
 * AutoRefresh - Polling automático para actualización de datos sin recargar página
 * 
 * Uso:
 * const refresh = new AutoRefresh({
 *     interval: 5000,
 *     endpoint: '/api/datos',
 *     onData: (data) => { ... },
 *     onError: (err) => { ... }
 * });
 * refresh.start();
 */
class AutoRefresh {
    constructor(config = {}) {
        this.interval = config.interval || 5000;
        this.endpoint = config.endpoint;
        this.onData = config.onData;
        this.onError = config.onError || console.error;
        this.timer = null;
        this.isRunning = false;
        this.lastUpdate = null;
    }

    async fetchData() {
        try {
            const response = await fetch(this.endpoint, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) throw new Error(`HTTP ${response.status}`);

            const data = await response.json();
            this.lastUpdate = new Date();
            this.onData(data);
        } catch (err) {
            this.onError(err);
        }
    }

    start() {
        if (this.isRunning) return;
        this.isRunning = true;
        this.fetchData();
        this.timer = setInterval(() => this.fetchData(), this.interval);
    }

    stop() {
        clearInterval(this.timer);
        this.isRunning = false;
    }

    restart() {
        this.stop();
        this.start();
    }
}
