/**
 * HabitacionCache - Módulo para gestión de caché
 * TTL configurable por tipo de dato
 */

const HabitacionCache = (function() {
    'use strict';

    const DEFAULT_TTL = 30000; // 30 segundos
    const DETAIL_TTL = 60000; // 1 minuto para detalles

    const storage = new Map();

    function isExpired(entry) {
        return Date.now() > entry.expiresAt;
    }

    return {
        get(key) {
            const entry = storage.get(key);
            if (!entry || isExpired(entry)) {
                storage.delete(key);
                return null;
            }
            return entry.data;
        },

        set(key, data, ttl = DEFAULT_TTL) {
            storage.set(key, {
                data,
                expiresAt: Date.now() + ttl,
            });
        },

        has(key) {
            const entry = storage.get(key);
            if (!entry || isExpired(entry)) {
                storage.delete(key);
                return false;
            }
            return true;
        },

        invalidate(key) {
            storage.delete(key);
        },

        invalidateAll() {
            storage.clear();
        },

        invalidatePattern(pattern) {
            for (const key of storage.keys()) {
                if (key.includes(pattern)) {
                    storage.delete(key);
                }
            }
        },

        makeKey(prefix, id) {
            return `${prefix}:${id}`;
        },

        DEFAULT_TTL,
        DETAIL_TTL,
    };
})();
