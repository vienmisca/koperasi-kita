<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('stockSystem', () => ({
            // Data state
            showForm: false,
            mode: 'existing',
            today: new Date().toISOString().split('T')[0],
            showKategoriModal: false,
            showEditModal: false, 
            editItem: { id_barang: null, nama_barang: '', harga_beli: '', harga_jual: '', stok_minimal: '', status: 'aktif', id_kategori: '', satuan: '' },
            kategoriBaru: {
                kode_kategori: '',
                nama_kategori: '',
                deskripsi: ''
            },
    
            // Form data
            form: {
                tanggal_masuk: new Date().toISOString().split('T')[0],
                keterangan: ''
            },
            
            // Barang existing
            barangList: @json($barang),
            filteredBarang: [],
            searchQuery: '',
            selectedBarang: null,
            existingItem: {
                jumlah: 1,
                harga_beli_baru: ''
            },
            
            // Barang baru
            newItem: {
                kode_barang: '',
                nama_barang: '',
                id_kategori: '',
                harga_beli: '',
                harga_jual: '',
                stok_awal: 0,
                stok_minimal: 10,
                satuan: '',
                deskripsi: '',
                gambar: null
            },
    
            imagePreview: null,
    
            // Table filtering (initialized in init)
            tableSearch: '',
            tableCategory: '',
            
            // Computed
            get canSubmit() {
                if (this.mode === 'existing') {
                    return this.selectedBarang && this.existingItem.jumlah > 0;
                } else {
                    return this.newItem.kode_barang && 
                           this.newItem.nama_barang && 
                           this.newItem.id_kategori && 
                           this.newItem.harga_beli > 0 && 
                           this.newItem.harga_jual > 0 &&
                           this.newItem.satuan &&
                           this.newItem.stok_minimal > 0;
                }
            },
            
            // Methods
            init() {
                // Initial filter to sync list
                this.filteredBarang = this.barangList;
                console.log('Stock System Initialized', this.barangList);
            },
    
            openEditModal(id) {
                const barang = this.barangList.find(b => b.id_barang == id);
    
                if (!barang) {
                    this.notify('error', 'Data barang tidak ditemukan!');
                    return;
                }
    
                this.editItem = {
                    id_barang: barang.id_barang,
                    nama_barang: barang.nama_barang,
                    id_kategori: barang.id_kategori,
                    harga_beli: barang.harga_beli,
                    harga_jual: barang.harga_jual,
                    stok_minimal: barang.stok_minimal,
                    satuan: barang.satuan,
                    status: barang.status || 'aktif'
                };
    
                this.showEditModal = true;
            },
    
            searchBarang() {
                if (!this.searchQuery.trim()) {
                    this.filteredBarang = this.barangList;
                    return;
                }
                
                const query = this.searchQuery.toLowerCase();
                this.filteredBarang = this.barangList.filter(barang => {
                    return barang.nama_barang.toLowerCase().includes(query) ||
                           barang.kode_barang.toLowerCase().includes(query);
                });
            },
            
            selectExistingBarang(barang) {
                this.selectedBarang = barang;
                this.existingItem.harga_beli_baru = barang.harga_beli;
            },
            
            async processBarangMasuk() {
                if (!this.canSubmit) return;
                
                const formData = new FormData();
                formData.append('tanggal_masuk', this.form.tanggal_masuk);
                formData.append('keterangan', this.form.keterangan);
                
                if (this.mode === 'existing') {
                    formData.append('mode', 'existing');
                    formData.append('id_barang', this.selectedBarang.id_barang);
                    formData.append('jumlah', this.existingItem.jumlah);
                    formData.append('harga_beli_baru', this.existingItem.harga_beli_baru || this.selectedBarang.harga_beli);
                } else {
                    formData.append('mode', 'new');
                    formData.append('kode_barang', this.newItem.kode_barang);
                    formData.append('nama_barang', this.newItem.nama_barang);
                    formData.append('id_kategori', this.newItem.id_kategori);
                    formData.append('harga_beli', this.newItem.harga_beli);
                    formData.append('harga_jual', this.newItem.harga_jual);
                    formData.append('stok_awal', this.newItem.stok_awal);
                    formData.append('stok_minimal', this.newItem.stok_minimal);
                    formData.append('satuan', this.newItem.satuan);
                    formData.append('deskripsi', this.newItem.deskripsi);
                }
                if (this.newItem.gambar) {
                    formData.append('gambar', this.newItem.gambar);
                }
                
                try {
                    const response = await fetch('{{ route("stock.store") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: formData
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        this.notify('success', 'Berhasil: ' + result.message);
                        this.closeModal();
                        setTimeout(() => location.reload(), 1500); 
                    } else {
                        this.notify('error', 'Gagal: ' + result.message);
                    }
                } catch (error) {
                    console.error(error);
                    this.notify('error', 'Error: ' + error.message);
                }
            },
            
            closeModal() {
                this.showForm = false;
                setTimeout(() => {
                     this.resetForm();
                }, 300); // Wait for transition
            },
            
            resetForm() {
                this.mode = 'existing';
                this.selectedBarang = null;
                this.existingItem = { jumlah: 1, harga_beli_baru: '' };
                this.newItem = {
                    kode_barang: '',
                    nama_barang: '',
                    id_kategori: '',
                    harga_beli: '',
                    harga_jual: '',
                    stok_awal: 0,
                    stok_minimal: 10,
                    satuan: '',
                    deskripsi: ''
                };
                this.searchQuery = '';
                this.filteredBarang = this.barangList;
                this.imagePreview = null;
            },
            
            // Helper for Notifications
            notify(type, message) {
                window.dispatchEvent(new CustomEvent('notify', { 
                    detail: { type, message } 
                }));
            },

            tambahStokBarang(id, nama) {
                this.showForm = true;
                this.mode = 'existing';
                
                // Cari barang berdasarkan ID
                const barang = this.barangList.find(b => b.id_barang == id);
                if (barang) {
                    // Small delay to allow modal to open then select
                    setTimeout(() => {
                        this.selectExistingBarang(barang);
                    }, 100);
                }
            },
            
            filterTable() {
                const searchTerm = this.tableSearch.toLowerCase();
                const category = this.tableCategory.toLowerCase();
                
                const rows = document.querySelectorAll('#barangTableBody tr');
                rows.forEach(row => {
                    const name = row.getAttribute('data-name');
                    const cat = row.getAttribute('data-category');
                    
                    const matchesSearch = !searchTerm || (name && name.includes(searchTerm));
                    const matchesCategory = !category || (cat && cat.includes(category));
                    
                    row.style.display = matchesSearch && matchesCategory ? '' : 'none';
                });
            },
            
            formatNumber(num) {
                return new Intl.NumberFormat('id-ID').format(num);
            },
    
            previewImage(event) {
                const file = event.target.files[0];
                if (!file) return;
        
                this.newItem.gambar = file;
        
                const reader = new FileReader();
                reader.onload = e => {
                    this.imagePreview = e.target.result;
                };
                reader.readAsDataURL(file);
            },
    
            async submitEditBarang() {
                try {
                    const response = await fetch(`/stock/${this.editItem.id_barang}`, {
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(this.editItem)
                    });
            
                    const result = await response.json();
            
                    if (result.success) {
                        this.notify('success', 'Barang berhasil diupdate!');
                        this.showEditModal = false;
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        this.notify('error', 'Gagal update: ' + result.message);
                    }
                } catch (error) {
                    this.notify('error', 'Error: ' + error.message);
                }
            },
            
            async hapusBarang(id, nama) {
                if (!confirm(`Yakin ingin menghapus ${nama}?`)) return;
            
                try {
                    const response = await fetch(`/stock/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });
            
                    const result = await response.json();
            
                    if (result.success) {
                        this.notify('success', 'Barang berhasil dihapus!');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        this.notify('error', 'Gagal hapus: ' + result.message);
                    }
                } catch (error) {
                    this.notify('error', 'Error: ' + error.message);
                }
            },
            
            async submitKategori() {
                if (!this.kategoriBaru.kode_kategori || !this.kategoriBaru.nama_kategori) {
                    this.notify('warning', 'Kode dan Nama Kategori wajib diisi!');
                    return;
                }
            
                try {
                    const response = await fetch('{{ route("kategori.store") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(this.kategoriBaru)
                    });
            
                    const result = await response.json();
            
                    if (result.success) {
                        this.notify('success', 'Kategori berhasil ditambahkan!');
                        this.showKategoriModal = false;
            
                        this.kategoriBaru = {
                            kode_kategori: '',
                            nama_kategori: '',
                            deskripsi: ''
                        };
            
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        this.notify('error', 'Gagal tambah kategori: ' + result.message);
                    }
                } catch (error) {
                    this.notify('error', 'Error: ' + error.message);
                }
            }
        }));
    });
</script>
