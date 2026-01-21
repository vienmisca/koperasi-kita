{{-- Loading Screen Component --}}
<div id="global-loading-screen" 
     class="fixed inset-0 z-[9999] flex items-center justify-center bg-white dark:bg-gray-900 transition-opacity duration-300 opacity-0 pointer-events-none">
    
    <div class="text-center p-8 rounded-2xl shadow-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
        {{-- Spinner --}}
        <div class="relative mx-auto mb-6">
            <div class="w-20 h-20 border-4 border-blue-100 rounded-full"></div>
            <div class="absolute top-0 left-0 w-20 h-20 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
        </div>
        
        {{-- Loading Text --}}
        <div class="space-y-3">
            <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200" id="loading-title">Memuat...</h3>
            <p class="text-gray-600 dark:text-gray-400 text-sm" id="loading-message">Mohon tunggu sebentar</p>
        </div>
        
        {{-- Progress Bar --}}
        <div class="mt-6 w-64 mx-auto hidden" id="loading-progress-container">
            <div class="flex justify-between text-sm text-gray-500 mb-1">
                <span>Progress</span>
                <span id="loading-percentage">0%</span>
            </div>
            <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                <div id="loading-progress-bar" class="h-full bg-gradient-to-r from-blue-500 to-purple-600 rounded-full transition-all duration-300" style="width: 0%"></div>
            </div>
        </div>
        
        {{-- Cancel Button --}}
        <button id="loading-cancel-btn" 
                class="mt-6 px-5 py-2 text-sm font-medium text-red-600 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors hidden">
            Batalkan
        </button>
    </div>
    
    {{-- Loading untuk halaman (lebih sederhana) --}}
    <div id="page-loading-overlay" 
         class="fixed inset-0 z-[9998] bg-white/80 dark:bg-gray-900/80 backdrop-blur-sm flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300">
        <div class="w-12 h-12 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
    </div>
</div>

{{-- Loading Script --}}
<script>
// Global Loading Screen Manager
class LoadingScreen {
    constructor() {
        this.loadingEl = document.getElementById('global-loading-screen');
        this.pageLoadingEl = document.getElementById('page-loading-overlay');
        this.progressBar = document.getElementById('loading-progress-bar');
        this.progressContainer = document.getElementById('loading-progress-container');
        this.percentageEl = document.getElementById('loading-percentage');
        this.titleEl = document.getElementById('loading-title');
        this.messageEl = document.getElementById('loading-message');
        this.cancelBtn = document.getElementById('loading-cancel-btn');
        this.isShowing = false;
        this.cancelCallback = null;
        
        this.init();
    }
    
    init() {
        // Intercept semua link klik untuk page navigation
        this.interceptLinks();
        
        // Intercept form submissions
        this.interceptForms();
        
        // Setup cancel button
        this.cancelBtn.addEventListener('click', () => this.cancel());
        
        // Listen untuk browser back/forward
        window.addEventListener('popstate', () => {
            this.showPageLoading();
        });
    }
    
    interceptLinks() {
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a');
            if (!link) return;
            
            // Skip jika:
            // 1. Link external (_blank)
            // 2. Link dengan hash (#)
            // 3. Link dengan atribut data-no-loading
            // 4. Link download
            // 5. Link javascript:
            if (link.target === '_blank' || 
                link.href.includes('#') || 
                link.hasAttribute('data-no-loading') ||
                link.hasAttribute('download') ||
                link.href.startsWith('javascript:')) {
                return;
            }
            
            // Cek jika link ke halaman yang berbeda
            const currentUrl = window.location.pathname;
            const linkUrl = new URL(link.href, window.location.origin);
            
            if (linkUrl.pathname !== currentUrl && linkUrl.origin === window.location.origin) {
                e.preventDefault();
                this.showPageLoading();
                
                // Simpan scroll position untuk nanti
                sessionStorage.setItem('scrollPosition', window.pageYOffset);
                
                // Go to page setelah 100ms (biar loading keliatan)
                setTimeout(() => {
                    window.location.href = link.href;
                }, 100);
            }
        });
    }
    
    interceptForms() {
        document.addEventListener('submit', (e) => {
            const form = e.target;
            
            // Skip form dengan atribut data-no-loading
            if (form.hasAttribute('data-no-loading')) return;
            
            this.show('Mengirim data...', 'Harap tunggu...');
        });
    }
    
    // ===== PUBLIC METHODS =====
    
    show(title = 'Memuat...', message = 'Mohon tunggu sebentar') {
        this.isShowing = true;
        this.titleEl.textContent = title;
        this.messageEl.textContent = message;
        this.progressContainer.classList.add('hidden');
        this.cancelBtn.classList.add('hidden');
        document.body.style.overflow = 'hidden';
        
        // Show loading
        this.loadingEl.classList.remove('opacity-0', 'pointer-events-none');
        this.loadingEl.classList.add('opacity-100');
    }
    
    hide() {
        this.isShowing = false;
        document.body.style.overflow = '';
        
        // Hide loading
        this.loadingEl.classList.remove('opacity-100');
        this.loadingEl.classList.add('opacity-0', 'pointer-events-none');
        
        // Reset progress
        this.updateProgress(0);
    }
    
    showPageLoading() {
        this.pageLoadingEl.classList.remove('opacity-0', 'pointer-events-none');
        this.pageLoadingEl.classList.add('opacity-100');
        document.body.style.overflow = 'hidden';
    }
    
    hidePageLoading() {
        this.pageLoadingEl.classList.remove('opacity-100');
        this.pageLoadingEl.classList.add('opacity-0', 'pointer-events-none');
        document.body.style.overflow = '';
        
        // Restore scroll position
        const scrollPosition = sessionStorage.getItem('scrollPosition');
        if (scrollPosition) {
            window.scrollTo(0, parseInt(scrollPosition));
            sessionStorage.removeItem('scrollPosition');
        }
    }
    
    updateProgress(percentage, message = null) {
        percentage = Math.max(0, Math.min(100, percentage));
        
        // Show progress bar jika belum
        if (percentage > 0 && this.progressContainer.classList.contains('hidden')) {
            this.progressContainer.classList.remove('hidden');
        }
        
        // Update progress
        this.progressBar.style.width = `${percentage}%`;
        this.percentageEl.textContent = `${Math.round(percentage)}%`;
        
        if (message) {
            this.messageEl.textContent = message;
        }
    }
    
    setCancelable(callback = null) {
        if (callback) {
            this.cancelCallback = callback;
            this.cancelBtn.classList.remove('hidden');
        } else {
            this.cancelBtn.classList.add('hidden');
        }
    }
    
    cancel() {
        if (this.cancelCallback && typeof this.cancelCallback === 'function') {
            this.cancelCallback();
        }
        this.hide();
    }
    
    // Helper untuk AJAX/Fetch
    async withLoading(promise, title = 'Memuat...', message = 'Mohon tunggu') {
        this.show(title, message);
        try {
            const result = await promise;
            return result;
        } finally {
            this.hide();
        }
    }
}

// Initialize ketika DOM siap
document.addEventListener('DOMContentLoaded', function() {
    // Create global loading instance
    window.Loading = new LoadingScreen();
    
    // Auto hide page loading ketika halaman selesai load
    window.Loading.hidePageLoading();
    
    // Jika ada elemen dengan data-loading="auto", show loading
    document.querySelectorAll('[data-loading="auto"]').forEach(el => {
        el.addEventListener('click', () => {
            const title = el.getAttribute('data-loading-title') || 'Memuat...';
            const message = el.getAttribute('data-loading-message') || 'Mohon tunggu';
            window.Loading.show(title, message);
        });
    });
});

// Fungsi global untuk akses mudah
window.showLoading = function(title, message) {
    if (window.Loading) {
        window.Loading.show(title, message);
    }
};

window.hideLoading = function() {
    if (window.Loading) {
        window.Loading.hide();
    }
};

window.showPageLoading = function() {
    if (window.Loading) {
        window.Loading.showPageLoading();
    }
};

window.hidePageLoading = function() {
    if (window.Loading) {
        window.Loading.hidePageLoading();
    }
};

window.updateLoadingProgress = function(percentage, message) {
    if (window.Loading) {
        window.Loading.updateProgress(percentage, message);
    }
};
</script>