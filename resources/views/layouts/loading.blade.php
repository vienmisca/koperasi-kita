{{-- Simple Fullscreen Loading --}}
<div id="loading-overlay" class="fixed inset-0 z-[9999] bg-white dark:bg-gray-900" style="display: none;">
    <div class="h-full flex flex-col items-center justify-center p-4">
        {{-- Animated Logo --}}
        <div class="relative mb-8">
            <div class="w-24 h-24 rounded-full border-4 border-gray-200 dark:border-gray-700 animate-pulse"></div>
            <div class="absolute top-0 left-0 w-24 h-24 rounded-full border-4 border-indigo-500 border-t-transparent animate-spin"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
                <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">K</span>
            </div>
        </div>
        
        {{-- Loading Text --}}
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-2" id="loading-title">Memuat...</h2>
            <p class="text-gray-600 dark:text-gray-300" id="loading-message">Harap tunggu sebentar</p>
        </div>
        
        {{-- Progress Bar --}}
        <div class="w-64 mb-4 hidden" id="loading-progress-container">
            <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                <div id="loading-progress-bar" class="h-full bg-indigo-500 rounded-full transition-all duration-300" style="width: 0%"></div>
            </div>
            <div class="flex justify-between text-sm mt-1">
                <span class="text-gray-600 dark:text-gray-400">Progress</span>
                <span class="font-medium text-gray-800 dark:text-gray-200" id="loading-percentage">0%</span>
            </div>
        </div>
        
        {{-- Animated Dots --}}
        <div class="flex space-x-2" id="loading-dots">
            <div class="w-3 h-3 rounded-full bg-indigo-400 animate-bounce"></div>
            <div class="w-3 h-3 rounded-full bg-indigo-400 animate-bounce" style="animation-delay: 0.2s"></div>
            <div class="w-3 h-3 rounded-full bg-indigo-400 animate-bounce" style="animation-delay: 0.4s"></div>
        </div>
        
        {{-- Cancel Button --}}
        <button id="loading-cancel-btn" class="mt-8 px-6 py-2 text-sm font-medium text-red-600 hover:text-red-700 rounded-lg border border-red-200 hover:bg-red-50 dark:border-red-800 dark:text-red-400 dark:hover:text-red-300 dark:hover:bg-red-900/20 transition-colors hidden">
            Batalkan
        </button>
    </div>
</div>

{{-- Quick Page Loader --}}
<div id="quick-loader" class="fixed inset-0 z-[9998] bg-white/90 dark:bg-gray-900/90 backdrop-blur-sm flex items-center justify-center" style="display: none;">
    <div class="text-center">
        <div class="w-12 h-12 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin mx-auto"></div>
        <p class="mt-4 text-gray-600 dark:text-gray-400">Mengalihkan...</p>
    </div>
</div>

<script>
// Simple Loading Manager
class LoadingManager {
    constructor() {
        this.overlay = document.getElementById('loading-overlay');
        this.quickLoader = document.getElementById('quick-loader');
        this.title = document.getElementById('loading-title');
        this.message = document.getElementById('loading-message');
        this.progressBar = document.getElementById('loading-progress-bar');
        this.progressContainer = document.getElementById('loading-progress-container');
        this.percentage = document.getElementById('loading-percentage');
        this.dots = document.getElementById('loading-dots');
        this.cancelBtn = document.getElementById('loading-cancel-btn');
        
        this.isShowing = false;
        this.cancelCallback = null;
        
        this.init();
    }
    
    init() {
        // Setup cancel button
        this.cancelBtn.addEventListener('click', () => this.cancel());
        
        // Setup link interception
        this.setupLinkInterception();
        
        // Auto hide quick loader on page load
        window.addEventListener('load', () => {
            this.hideQuickLoader();
        });
    }
    
    setupLinkInterception() {
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a');
            if (!link) return;
            
            // Skip specific cases
            if (link.target === '_blank' || 
                link.hasAttribute('download') ||
                link.hasAttribute('data-no-loading') ||
                link.href.startsWith('javascript:') ||
                link.href.includes('#') ||
                link.href.includes('logout')) {
                return;
            }
            
            const currentPath = window.location.pathname;
            const linkUrl = new URL(link.href, window.location.origin);
            
            if (linkUrl.pathname !== currentPath) {
                e.preventDefault();
                this.showQuickLoader();
                
                setTimeout(() => {
                    window.location.href = link.href;
                }, 200);
            }
        });
    }
    
    // Public Methods
    show(title = 'Memuat...', message = 'Harap tunggu sebentar') {
        this.isShowing = true;
        this.title.textContent = title;
        this.message.textContent = message;
        
        this.progressContainer.classList.add('hidden');
        this.dots.classList.remove('hidden');
        this.cancelBtn.classList.add('hidden');
        
        document.body.style.overflow = 'hidden';
        this.overlay.style.display = 'flex';
    }
    
    hide() {
        this.isShowing = false;
        document.body.style.overflow = '';
        this.overlay.style.display = 'none';
        this.cancelCallback = null;
    }
    
    showWithProgress(title, message) {
        this.show(title, message);
        this.progressContainer.classList.remove('hidden');
        this.dots.classList.add('hidden');
        return this;
    }
    
    updateProgress(percent, customMessage = null) {
        percent = Math.max(0, Math.min(100, percent));
        this.progressBar.style.width = `${percent}%`;
        this.percentage.textContent = `${Math.round(percent)}%`;
        
        if (customMessage) {
            this.message.textContent = customMessage;
        }
        
        return this;
    }
    
    setCancelable(callback = null) {
        this.cancelCallback = callback;
        if (callback) {
            this.cancelBtn.classList.remove('hidden');
        } else {
            this.cancelBtn.classList.add('hidden');
        }
        return this;
    }
    
    showQuickLoader() {
        document.body.style.overflow = 'hidden';
        this.quickLoader.style.display = 'flex';
    }
    
    hideQuickLoader() {
        document.body.style.overflow = '';
        this.quickLoader.style.display = 'none';
    }
    
    cancel() {
        if (this.cancelCallback && typeof this.cancelCallback === 'function') {
            this.cancelCallback();
        }
        this.hide();
    }
    
    // Helper untuk AJAX
    async withLoading(promise, title = 'Memuat...', message = 'Harap tunggu') {
        this.show(title, message);
        try {
            const result = await promise;
            return result;
        } finally {
            this.hide();
        }
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    // Create global loading instance
    window.loading = new LoadingManager();
    
    // Setup AJAX interception
    setupAjaxInterception();
});

// AJAX/Fetch interception
function setupAjaxInterception() {
    // Intercept fetch
    const originalFetch = window.fetch;
    window.fetch = async function(...args) {
        let loadingShown = false;
        const loadingTimeout = setTimeout(() => {
            window.loading.show('Memuat data...', 'Sedang mengambil data dari server');
            loadingShown = true;
        }, 500);
        
        try {
            const response = await originalFetch(...args);
            return response;
        } finally {
            clearTimeout(loadingTimeout);
            if (loadingShown) {
                window.loading.hide();
            }
        }
    };
    
    // Intercept XMLHttpRequest
    const originalXHROpen = XMLHttpRequest.prototype.open;
    const originalXHRSend = XMLHttpRequest.prototype.send;
    
    XMLHttpRequest.prototype.open = function() {
        this._url = arguments[1];
        return originalXHROpen.apply(this, arguments);
    };
    
    XMLHttpRequest.prototype.send = function() {
        const url = this._url;
        
        // Skip untuk request tertentu
        if (url && url.includes('/api/')) {
            let loadingShown = false;
            const loadingTimeout = setTimeout(() => {
                window.loading.show('Memproses...', 'Sedang berkomunikasi dengan server');
                loadingShown = true;
            }, 500);
            
            this.addEventListener('loadend', function() {
                clearTimeout(loadingTimeout);
                if (loadingShown) {
                    window.loading.hide();
                }
            });
        }
        
        return originalXHRSend.apply(this, arguments);
    };
}

// Global helper functions
window.showLoading = function(title, message) {
    if (window.loading) {
        window.loading.show(title, message);
    }
};

window.hideLoading = function() {
    if (window.loading) {
        window.loading.hide();
    }
};

window.showLoadingWithProgress = function(title, message) {
    if (window.loading) {
        return window.loading.showWithProgress(title, message);
    }
};

window.showQuickLoader = function() {
    if (window.loading) {
        window.loading.showQuickLoader();
    }
};

window.hideQuickLoader = function() {
    if (window.loading) {
        window.loading.hideQuickLoader();
    }
};
</script>

<style>
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

.animate-spin {
    animation: spin 1s linear infinite;
}

.animate-bounce {
    animation: bounce 0.6s infinite;
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

/* Prevent body scroll when loading is active */
body.loading-active {
    overflow: hidden !important;
    height: 100vh !important;
}
</style>