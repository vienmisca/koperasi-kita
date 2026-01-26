{{-- Minimal Top Progress Loader --}}
<div id="global-loader" class="fixed top-0 left-0 w-full z-[9999] pointer-events-none" style="display: none;">
    <div class="h-1 w-full bg-transparent overflow-hidden">
        <div id="loader-bar" class="h-full bg-indigo-600 animate-progress"></div>
    </div>
</div>

<style>
    @keyframes progress {
        0% { transform: translateX(-100%); }
        50% { transform: translateX(-20%); }
        100% { transform: translateX(0%); }
    }
    .animate-progress {
        animation: progress 1.5s ease-in-out infinite;
        width: 100%;
        transform-origin: left;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const loader = document.getElementById('global-loader');
        
        window.showLoading = () => {
             loader.style.display = 'block';
        };

        window.hideLoading = () => {
             loader.style.display = 'none';
        };
        
        // Optional: Show on simple navigation if desired, but user asked to remove "annoying" loading.
        // We will only show it on AJAX if explicitly called or for very long processes.
        
        // Intercept Fetch for subtle feedback
        const originalFetch = window.fetch;
        window.fetch = async function(...args) {
            window.showLoading();
            try {
                return await originalFetch(...args);
            } finally {
                window.hideLoading();
            }
        };
    });
</script>