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
            kategoriList: @json($kategori),
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
                    kode_barang: barang.kode_barang, // Added kode_barang
                    nama_barang: barang.nama_barang,
                    id_kategori: barang.id_kategori,
                    harga_beli: barang.harga_beli,
                    harga_jual: barang.harga_jual,
                    stok_minimal: barang.stok_minimal,
                    satuan: barang.satuan,
                    status: barang.status || 'aktif',
                    gambar: barang.gambar, // Added gambar
                    imagePreview: null,
                    imageFile: null // New file input holder
                };
    
                this.showEditModal = true;
            },
           
            generateRandomCode(target = 'new') {
                // Generate 13 digit number (like EAN-13)
                // Start with 899 (Indonesia prefix usually) + 10 random digits
                const random = '899' + Math.floor(Math.random() * 10000000000).toString().padStart(10, '0');
                
                if(target === 'new') {
                    this.newItem.kode_barang = random;
                } else {
                    this.editItem.kode_barang = random;
                }
            },

            printBarcodeWithQty(code, name) {
                if (!code) return;
                // Default start with 10, user can change in the window
                this.printBarcode(code, name, 10);
            },

            printBarcode(code, name, initialQty = 1) {
                if (!code) return;
                const url = `https://bwipjs-api.metafloor.com/?bcid=code128&text=${code}&scale=2&includetext&textxalign=center`;
                
                const win = window.open('', '_blank', 'width=900,height=700');
                
                const htmlContent = `
                    <html>
                    <head>
                        <title>Cetak Barcode - ${name}</title>
                        <style>
                            @page { size: auto; margin: 5mm; }
                            body { font-family: sans-serif; background: #f9fafb; margin: 0; padding: 20px; }
                            
                            /* Controls (Hidden when printing) */
                            .controls {
                                position: sticky;
                                top: 0;
                                background: white;
                                padding: 15px;
                                border-radius: 12px;
                                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                                display: flex;
                                gap: 15px;
                                align-items: center;
                                justify-content: center;
                                margin-bottom: 25px;
                                z-index: 100;
                            }
                            .qty-group {
                                display: flex;
                                items-center;
                                gap: 0;
                                border: 1px solid #d1d5db;
                                border-radius: 8px;
                                overflow: hidden;
                            }
                            .btn-qty {
                                padding: 10px 15px;
                                background: #f3f4f6;
                                border: none;
                                cursor: pointer;
                                font-weight: bold;
                                font-size: 16px;
                                color: #374151;
                                transition: background 0.2s;
                            }
                            .btn-qty:hover { background: #e5e7eb; }
                            .btn-qty:active { background: #d1d5db; }
                            #qtyInput {
                                width: 60px;
                                text-align: center;
                                border: none;
                                border-left: 1px solid #d1d5db;
                                border-right: 1px solid #d1d5db;
                                padding: 5px;
                                font-weight: bold;
                                font-size: 16px;
                            }
                            .btn-print {
                                background: #2563eb;
                                color: white;
                                border: none;
                                padding: 10px 24px;
                                border-radius: 8px;
                                font-weight: bold;
                                cursor: pointer;
                                box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.3);
                                transition: background 0.2s;
                                display: flex;
                                align-items: center;
                                gap: 8px;
                            }
                            .btn-print:hover { background: #1d4ed8; }
                            
                            /* Grid Layout */
                            .grid-container {
                                display: grid;
                                grid-template-columns: repeat(3, 1fr);
                                gap: 10px;
                                max-width: 210mm; /* A4 width approx */
                                margin: 0 auto;
                                background: white;
                                padding: 20px;
                                box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                            }
                            .label-item {
                                border: 1px dashed #e5e7eb;
                                padding: 10px;
                                text-align: center;
                                page-break-inside: avoid;
                                display: flex;
                                flex-direction: column;
                                align-items: center;
                                justify-content: center;
                                height: 120px;
                                background: white;
                                border-radius: 8px;
                            }
                            .label-name { font-size: 10px; font-weight: bold; margin-bottom: 5px; height:24px; overflow:hidden; color: #1f2937; }
                            .label-img { max-width: 100%; height: 50px; }
                            .label-code { font-size: 10px; font-family: monospace; margin-top: 2px; color: #4b5563; }
                            
                            @media print {
                                body { background: white; padding: 0; }
                                .controls { display: none !important; }
                                .grid-container { box-shadow: none; padding: 0; max-width: none; }
                                .label-item { border: none; outline: 1px dashed #eee; border-radius: 0; }
                            }
                        </style>
                    </head>
                    <body>
                        <div class="controls">
                            <div class="qty-group">
                                <button class="btn-qty" onclick="updateQty(-1)">-</button>
                                <input type="number" id="qtyInput" value="${initialQty}" min="1" onchange="renderItems()">
                                <button class="btn-qty" onclick="updateQty(1)">+</button>
                            </div>
                            <button class="btn-print" onclick="window.print()">
                                <span>🖨️ Cetak</span>
                            </button>
                        </div>
                        
                        <div class="grid-container" id="gridContainer">
                            <!-- Items rendered here -->
                        </div>

                        <script>
                            const code = "${code}";
                            const name = "${name}";
                            const url = "${url}";
                            
                            function updateQty(change) {
                                const input = document.getElementById('qtyInput');
                                let newVal = parseInt(input.value) + change;
                                if (newVal < 1) newVal = 1;
                                input.value = newVal;
                                renderItems();
                            }

                            function renderItems() {
                                const qty = parseInt(document.getElementById('qtyInput').value) || 1;
                                const container = document.getElementById('gridContainer');
                                let html = '';
                                
                                for(let i=0; i<qty; i++) {
                                    html += \`
                                        <div class="label-item">
                                            <div class="label-name">\${name.substring(0, 20)}</div>
                                            <img src="\${url}" class="label-img">
                                            <div class="label-code">\${code}</div>
                                        </div>
                                    \`;
                                }
                                container.innerHTML = html;
                            }
                            
                            // Initial Render
                            renderItems();
                        <\/script>
                    </body>
                    </html>
                `;
                
                win.document.write(htmlContent);
                win.document.close();
            },

            previewEditImage(event) {
                const file = event.target.files[0];
                if (!file) return;

                this.editItem.imageFile = file;

                const reader = new FileReader();
                reader.onload = e => {
                    this.editItem.imagePreview = e.target.result;
                };
                reader.readAsDataURL(file);
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
                    // Changed to FormData for Image Upload
                    const formData = new FormData();
                    formData.append('_method', 'PUT'); // Trick for Laravel PUT via POST
                    formData.append('kode_barang', this.editItem.kode_barang);
                    formData.append('nama_barang', this.editItem.nama_barang);
                    formData.append('id_kategori', this.editItem.id_kategori);
                    formData.append('harga_beli', this.editItem.harga_beli);
                    formData.append('harga_jual', this.editItem.harga_jual);
                    formData.append('stok_minimal', this.editItem.stok_minimal);
                    formData.append('satuan', this.editItem.satuan);
                    formData.append('status', this.editItem.status);
                    
                    if (this.editItem.imageFile) {
                        formData.append('gambar', this.editItem.imageFile);
                    }

                    const response = await fetch(`/stock/${this.editItem.id_barang}`, {
                        method: 'POST', // Use POST with _method=PUT for files
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                            // Do NOT set Content-Type for FormData, browser does it
                        },
                        body: formData
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
                        
                        // Add to local list immediately
                        this.kategoriList.push(result.data);
                        
                        this.kategoriBaru = {
                            kode_kategori: '',
                            nama_kategori: '',
                            deskripsi: ''
                        };
            
                    } else {
                        this.notify('error', 'Gagal tambah kategori: ' + result.message);
                    }
                } catch (error) {
                    this.notify('error', 'Error: ' + error.message);
                }
            },

            async hapusKategori(id, name) {
                if (!confirm(`Yakin ingin menghapus kategori ${name}?`)) return;

                try {
                    const response = await fetch(`/kategori/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });

                    const result = await response.json();

                    if (result.success) {
                        this.notify('success', 'Kategori berhasil dihapus!');
                        // Remove from local list
                        this.kategoriList = this.kategoriList.filter(k => k.id_kategori != id);
                    } else {
                        this.notify('error', result.message);
                    }
                } catch (error) {
                    this.notify('error', 'Error: ' + error.message);
                }
            }
        }));
    });
</script>
