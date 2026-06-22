/**
 * NEXUS CORE - Master Front-end Engine v1.0.0
 * Configured for Global State Management (<300ms SLA)
 */

const { createApp, ref, computed } = Vue;

window.initNexusMasterEngine = function (additionalSetup = null) {
    const app = createApp({
        setup() {
            // 1. Master Layout States
            const isSidebarCollapsed = ref(false);
            const currentYear = ref(new Date().getFullYear());
            const searchQuery = ref('');

            // 2. Master Layout Actions
            const toggleSidebar = () => {
                isSidebarCollapsed.value = !isSidebarCollapsed.value;
            };

            // 3. Core Metrics Template Dataset (Populated dynamically via layout)
            const metrics = ref([
                { title: 'Today Sales', value: 'Rp 0', trend: 'Stable', icon: '💰', isPositive: true },
                { title: 'Transactions', value: '0', trend: 'Stable', icon: '🧾', isPositive: true }
            ]);

            // 4. Extensibility Hook for Child View Scripts
            let extendedSetup = {};
            if (additionalSetup && typeof additionalSetup === 'function') {
                extendedSetup = additionalSetup({ ref, computed, metrics, searchQuery });
            }

            // 5. Merge Core States with Extended Page States
            return {
                isSidebarCollapsed,
                currentYear,
                searchQuery,
                metrics,
                toggleSidebar,
                ...extendedSetup
            };
        }
    });

    return app;
};
