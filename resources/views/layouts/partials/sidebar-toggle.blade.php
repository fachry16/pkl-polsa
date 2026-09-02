{{-- Sidebar Toggle Button on Vertical Right Border --}}
<button type="button"
        @click="toggleSidebar()"
        class="sidebar-toggle-btn"
        :title="sidebarOpen ? 'Tutup Sidebar (<)' : 'Buka Sidebar (>)'"
        aria-label="Toggle Sidebar">
    <template x-if="sidebarOpen">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
    </template>
    <template x-if="!sidebarOpen">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="9 18 15 12 9 6"></polyline>
        </svg>
    </template>
</button>