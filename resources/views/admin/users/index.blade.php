@extends('layouts.admin')

@section('content')
<div x-data="userManagement()" class="p-6">
    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                Kelola Pengguna
            </h1>
            <p class="text-gray-500 mt-1">Atur akun admin dan kasir sistem.</p>
        </div>
        <button @click="openCreateModal()" 
                class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:shadow-indigo-300 transition-all transform hover:-translate-y-0.5 active:translate-y-0 flex items-center gap-2 font-bold text-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Pengguna
        </button>
    </div>

    <!-- Users Table (Clean & Professional Style) -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 font-semibold text-gray-600 text-sm">Nama Lengkap</th>
                        <th class="px-6 py-4 font-semibold text-gray-600 text-sm">Email</th>
                        <th class="px-6 py-4 font-semibold text-gray-600 text-sm">Role</th>
                        <th class="px-6 py-4 font-semibold text-gray-600 text-sm">Tanggal Dibuat</th>
                        <th class="px-6 py-4 font-semibold text-gray-600 text-sm text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($users as $user)
                    <tr class="hover:bg-gray-50 transition-colors group {{ $user->pending_password ? 'bg-amber-50' : '' }}">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $user->name }}</div>
                            @if($user->pending_password)
                                <div class="mt-1 flex items-center gap-1 text-xs text-amber-600 font-bold animate-pulse">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                    Permintaan Reset Password
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-600 text-sm">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            @if($user->role === 'admin')
                                <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-purple-100 text-purple-700 border border-purple-200">
                                    ADMIN
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-green-100 text-green-700 border border-green-200">
                                    KASIR
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 font-mono">
                            {{ $user->created_at->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2 items-center">
                                @if($user->pending_password)
                                    <form action="{{ route('admin.users.approve-reset', $user->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-2 py-1 bg-green-600 hover:bg-green-700 text-white text-xs font-bold rounded shadow-sm mr-1" title="Setujui Reset Password">
                                            ✓ Setujui
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.users.reject-reset', $user->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-2 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded shadow-sm" title="Tolak Reset Password">
                                            ✗
                                        </button>
                                    </form>
                                @else
                                    <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button @click="openEditModal({{ $user }})" 
                                                class="p-1.5 text-blue-600 hover:bg-blue-100 rounded-md transition" 
                                                title="Edit User">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        
                                        @if(auth()->id() !== $user->id)
                                        <button @click="deleteUser({{ $user->id }}, '{{ $user->name }}')" 
                                                class="p-1.5 text-red-600 hover:bg-red-100 rounded-md transition" 
                                                title="Hapus User">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    
                    @if($users->isEmpty())
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            Belum ada pengguna lain.
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- IMPROVED POPUP / MODAL -->
    <div x-show="showModal" style="display: none;"
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
         
         <!-- Backdrop -->
         <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showModal = false"></div>

         <!-- Modal Card -->
         <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all"
              x-transition:enter="transition ease-out duration-300"
              x-transition:enter-start="opacity-0 translate-y-4 scale-95"
              x-transition:enter-end="opacity-100 translate-y-0 scale-100"
              x-transition:leave="transition ease-in duration-200"
              x-transition:leave-start="opacity-100 translate-y-0 scale-100"
              x-transition:leave-end="opacity-0 translate-y-4 scale-95">
              
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800" x-text="isEdit ? 'Edit Pengguna' : 'Tambah Pengguna Baru'"></h3>
                <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <form @submit.prevent="submitForm()" class="p-6 space-y-4">
                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </span>
                        <input type="text" x-model="form.name" required placeholder="Nama Lengkap"
                               class="pl-10 w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 shadow-sm text-sm">
                    </div>
                </div>
                
                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <div class="relative">
                         <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </span>
                        <input type="email" x-model="form.email" required placeholder="email@contoh.com"
                               class="pl-10 w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 shadow-sm text-sm">
                    </div>
                </div>
                
                <!-- Role -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Peran Akun (Role)</label>
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Role: Kasir -->
                        <label class="cursor-pointer">
                            <input type="radio" name="role" value="kasir" x-model="form.role" class="peer sr-only">
                            <div class="p-3 border-2 border-gray-100 rounded-xl text-center transition-all 
                                        peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-700 
                                        hover:border-indigo-200 hover:bg-gray-50">
                                <span class="block font-bold">Kasir</span>
                                <span class="text-xs text-gray-500 mt-1">Akses penjualan POS</span>
                            </div>
                        </label>
                        
                        <!-- Role: Admin -->
                        <label class="cursor-pointer">
                            <input type="radio" name="role" value="admin" x-model="form.role" class="peer sr-only">
                            <div class="p-3 border-2 border-gray-100 rounded-xl text-center transition-all 
                                        peer-checked:border-purple-500 peer-checked:bg-purple-50 peer-checked:text-purple-700 
                                        hover:border-purple-200 hover:bg-gray-50">
                                <span class="block font-bold">Admin</span>
                                <span class="text-xs text-gray-500 mt-1">Akses penuh sistem</span>
                            </div>
                        </label>
                    </div>
                </div>
                
                <!-- Password Field with Toggle -->
                <div x-data="{ showPass: false }">
                    <label class="block text-sm font-medium text-gray-700 mb-1" x-text="isEdit ? 'Password Baru (Opsional)' : 'Password'">
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </span>
                        <input :type="showPass ? 'text' : 'password'" x-model="form.password" :required="!isEdit" placeholder="********"
                               class="pl-10 pr-10 w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 shadow-sm text-sm">
                        <button type="button" @click="showPass = !showPass" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                             <svg x-show="!showPass" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                             <svg x-show="showPass" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div x-show="!isEdit || form.password.length > 0">
                    <label class="block text-sm font-medium text-gray-700 mb-1" x-text="isEdit ? 'Konfirmasi Password Baru' : 'Konfirmasi Password'"></label>
                     <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </span>
                        <input type="password" x-model="form.password_confirmation" :required="(!isEdit || form.password)" placeholder="Ulangi Password"
                               class="pl-10 w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 shadow-sm text-sm">
                    </div>
                </div>
                
                <div class="pt-4 flex justify-end gap-3">
                    <button type="button" @click="showModal = false" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg transition text-sm font-medium">Batal</button>
                    <button type="submit" 
                            class="px-4 py-2 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 shadow transition transform active:scale-95 text-sm">
                        Simpan
                    </button>
                </div>
            </form>
         </div>
    </div>
</div>

<script>
    function userManagement() {
        return {
            showModal: false,
            isEdit: false,
            form: {
                id: null,
                name: '',
                email: '',
                role: 'kasir',
                password: '',
                password_confirmation: ''
            },
            
            openCreateModal() {
                this.isEdit = false;
                this.form = {
                    id: null,
                    name: '',
                    email: '',
                    role: 'kasir',
                    password: '',
                    password_confirmation: ''
                };
                this.showModal = true;
            },
            
            openEditModal(user) {
                this.isEdit = true;
                this.form = {
                    id: user.id,
                    name: user.name,
                    email: user.email,
                    role: user.role,
                    password: '',
                    password_confirmation: ''
                };
                this.showModal = true;
            },
            
            notify(type, message) {
                window.dispatchEvent(new CustomEvent('notify', { 
                    detail: { type, message } 
                }));
            },
            
            async submitForm() {
                try {
                    const url = this.isEdit ? `/users/${this.form.id}` : '{{ route("users.store") }}';
                    const method = this.isEdit ? 'PUT' : 'POST';
                    
                    const response = await fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(this.form)
                    });
                    
                    const result = await response.json();
                    
                    if (response.ok && result.success) {
                        this.notify('success', result.message);
                        this.showModal = false;
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        throw new Error(result.message || 'Terjadi kesalahan validasi');
                    }
                } catch (error) {
                    this.notify('error', error.message);
                    console.error(error);
                }
            },
            
            async deleteUser(id, name) {
                if (!confirm(`Yakin ingin menghapus user ${name}?`)) return;
                
                try {
                    const response = await fetch(`/users/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });
                     
                    const result = await response.json();
                    
                    if (response.ok && result.success) {
                        this.notify('success', result.message);
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        throw new Error(result.message || 'Gagal menghapus user');
                    }
                } catch (error) {
                    this.notify('error', error.message);
                }
            }
        }
    }
</script>
@endsection
