@extends('layouts.admin')

@section('content')
<div x-data="userManagement()" class="p-6">
    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                Kelola Pengguna
            </h1>
            <p class="text-gray-600 mt-2">Atur akun admin dan kasir sistem.</p>
        </div>
        <button @click="openCreateModal()" 
                class="px-5 py-3 bg-blue-600 text-white rounded-xl shadow-lg shadow-blue-200 hover:bg-blue-700 transition-all flex items-center gap-2 font-bold text-sm transform active:scale-95">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Pengguna
        </button>
    </div>

    <!-- Users Table -->
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
                    <tr class="hover:bg-blue-50/50 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $user->name }}</div>
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            @if($user->role === 'admin')
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-700 border border-purple-200">
                                    ADMIN
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700 border border-blue-200">
                                    KASIR
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 font-mono">
                            {{ $user->created_at->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button @click="openEditModal({{ $user }})" 
                                        class="p-1.5 text-blue-600 hover:bg-blue-100 rounded-lg transition" 
                                        title="Edit User">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                
                                @if(auth()->id() !== $user->id)
                                <button @click="deleteUser({{ $user->id }}, '{{ $user->name }}')" 
                                        class="p-1.5 text-red-600 hover:bg-red-100 rounded-lg transition" 
                                        title="Hapus User">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
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

    <!-- MODAL FORM (Create/Edit) -->
    <div x-show="showModal" style="display: none;"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         x-transition.opacity>
         
         <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden"
              @click.away="showModal = false"
              x-transition:enter="transition ease-out duration-300"
              x-transition:enter-start="opacity-0 scale-95 translate-y-4"
              x-transition:enter-end="opacity-100 scale-100 translate-y-0">
              
            <div class="px-6 py-4 border-b bg-gray-50 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900" x-text="isEdit ? 'Edit Pengguna' : 'Tambah Pengguna Baru'"></h3>
                <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
            </div>
            
            <form @submit.prevent="submitForm()" class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Nama Lengkap</label>
                    <input type="text" x-model="form.name" required
                           class="w-full bg-gray-50 border-none rounded-xl px-4 py-3 text-gray-800 font-medium focus:ring-2 focus:ring-blue-500/20 transition-all placeholder-gray-400"
                           placeholder="Masukkan nama lengkap">
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Email</label>
                    <input type="email" x-model="form.email" required
                           class="w-full bg-gray-50 border-none rounded-xl px-4 py-3 text-gray-800 font-medium focus:ring-2 focus:ring-blue-500/20 transition-all placeholder-gray-400"
                           placeholder="contoh@email.com">
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">Role Akses</label>
                    <div class="grid grid-cols-2 gap-3">
                        <!-- Kasir Option -->
                        <div @click="form.role = 'kasir'" 
                             class="cursor-pointer rounded-xl p-3 border-2 transition-all flex flex-col items-center justify-center gap-2"
                             :class="form.role === 'kasir' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-gray-100 bg-white text-gray-500 hover:border-gray-200'">
                            <div class="p-2 rounded-full" :class="form.role === 'kasir' ? 'bg-blue-100' : 'bg-gray-100'">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </div>
                            <span class="font-bold text-sm">Kasir</span>
                        </div>
                        
                        <!-- Admin Option -->
                        <div @click="form.role = 'admin'" 
                             class="cursor-pointer rounded-xl p-3 border-2 transition-all flex flex-col items-center justify-center gap-2"
                             :class="form.role === 'admin' ? 'border-purple-500 bg-purple-50 text-purple-700' : 'border-gray-100 bg-white text-gray-500 hover:border-gray-200'">
                            <div class="p-2 rounded-full" :class="form.role === 'admin' ? 'bg-purple-100' : 'bg-gray-100'">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                            <span class="font-bold text-sm">Admin</span>
                        </div>
                    </div>
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5" x-text="isEdit ? 'Password Baru (Opsional)' : 'Password'"></label>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'" x-model="form.password" :required="!isEdit"
                               class="w-full bg-gray-50 border-none rounded-xl px-4 py-3 text-gray-800 font-medium focus:ring-2 focus:ring-blue-500/20 transition-all placeholder-gray-400"
                               placeholder="********">
                        <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 px-4 text-gray-400 hover:text-gray-600 focus:outline-none">
                            <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                        </button>
                    </div>
                    <p x-show="isEdit" class="text-[10px] text-gray-400 mt-1 font-medium">Biarkan kosong jika tidak ingin mengganti password.</p>
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5" x-text="isEdit ? 'Konfirmasi Password Baru' : 'Konfirmasi Password'"></label>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'" x-model="form.password_confirmation" :required="(!isEdit || form.password)"
                               class="w-full bg-gray-50 border-none rounded-xl px-4 py-3 text-gray-800 font-medium focus:ring-2 focus:ring-blue-500/20 transition-all placeholder-gray-400"
                               placeholder="********">
                    </div>
                </div>
                
                <div class="pt-6 flex justify-end gap-3">
                    <button type="button" @click="showModal = false" class="px-5 py-3 text-gray-500 font-bold hover:bg-gray-100 rounded-xl transition text-sm">Batal</button>
                    <button type="submit" 
                            class="px-6 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 shadow-lg shadow-blue-200 transition transform active:scale-95 text-sm flex items-center gap-2">
                        <span x-text="isEdit ? 'Simpan Perubahan' : 'Buat Pengguna'"></span>
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
            showPassword: false,
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
                this.showPassword = false;
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
                this.showPassword = false;
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
