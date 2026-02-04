<div x-data="{ 
    notifications: [],
    add(e) {
        this.notifications.push({
            id: Date.now(),
            message: e.detail.message,
            type: e.detail.type || 'info', // success, error, warning, info
            show: true
        });
        setTimeout(() => { this.remove(this.notifications.length - 1) }, 3000);
    },
    remove(index) {
        this.notifications[index].show = false;
        setTimeout(() => { this.notifications.splice(index, 1) }, 300);
    }
}"
@notify.window="add($event)"
class="fixed bottom-5 right-5 z-[100] flex flex-col gap-2 pointer-events-none">
    
    <template x-for="(note, index) in notifications" :key="note.id">
        <div x-show="note.show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-2"
             class="pointer-events-auto min-w-[300px] max-w-sm rounded-xl shadow-lg border p-4 flex items-center gap-3 backdrop-blur-sm"
             :class="{
                 'bg-white border-green-200 text-green-800': note.type === 'success',
                 'bg-white border-red-200 text-red-800': note.type === 'error',
                 'bg-white border-yellow-200 text-yellow-800': note.type === 'warning',
                 'bg-white border-blue-200 text-blue-800': note.type === 'info',
             }">
            
            <!-- Icon -->
            <div :class="{
                'bg-green-100 text-green-600': note.type === 'success',
                'bg-red-100 text-red-600': note.type === 'error',
                'bg-yellow-100 text-yellow-600': note.type === 'warning',
                'bg-blue-100 text-blue-600': note.type === 'info',
            }" class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0">
                <template x-if="note.type === 'success'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </template>
                <template x-if="note.type === 'error'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </template>
                <template x-if="note.type === 'warning'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </template>
                <template x-if="note.type === 'info'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </template>
            </div>

            <!-- Content -->
            <div class="flex-1">
                <p class="font-medium text-sm" x-text="note.message"></p>
            </div>

            <!-- Close -->
            <button @click="remove(notifications.indexOf(note))" class="text-gray-400 hover:text-gray-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </template>
</div>
