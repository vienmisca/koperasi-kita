<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('stockSystem', () => ({
            // ================= STATE =================
            showForm: false, 
            showEditModal: false,
            showKategoriModal: false,
            showImportModal: false,
            
            mode: 'existing',
            today: new Date().toISOString().split('T')[0],
            
            // Data Lists
            barangList: @json($barang->items() ?? []),
            kategoriList: @json($kategori ?? []),
            filteredBarang: [],
            
            // Filter Variables
            searchQuery: '',
            tableSearch: '{{ request("search") }}',
            tableCategory: '{{ is_numeric(request("kategori")) ? request("kategori") : (\App\Models\Kategori::where("nama_kategori", request("kategori"))->value("id_kategori") ?? "") }}',
            tableStatus: '{{ request("stok_status") }}',

            // Form Data
            form: {
                tanggal_masuk: new Date().toISOString().split('T')[0],
                keterangan: ''
            },
            
            // New Item Form
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
            
            // Edit Item Form
            editItem: { 
                id_barang: null, 
                kode_barang: '',
                nama_barang: '', 
                harga_beli: '', 
                harga_jual: '', 
                stok_minimal: '', 
                status: 'aktif', 
                id_kategori: '', 
                satuan: '',
                gambar: null,
                imagePreview: null,
                imageFile: null
            },

            // Other Forms
            existingItem: { jumlah: 1, harga_beli_baru: '' },
            kategoriBaru: { kode_kategori: '', nama_kategori: '', deskripsi: '' },
            
            // Temporary State
            selectedBarang: null,
            imagePreview: null,
            fileSelected: null,
            isImporting: false,

            // ================= COMPUTED =================
            get canSubmit() {
                if (this.mode === 'existing') {
                    return this.selectedBarang && this.existingItem.jumlah > 0;
                } else {
                    return this.newItem.kode_barang && 
                           this.newItem.nama_barang && 
                           this.newItem.id_kategori && 
                           this.newItem.harga_beli > 0 && 
                           this.newItem.stok_minimal > 0;
                }
            },
            
            // ================= METHODS =================
            init() {
                this.filteredBarang = this.barangList || [];
                console.log('Stock System Initialized v3.3 - Robust Mode');
                // Ensure all modals are closed on init
                this.closeAllModals();
            },

            // --- HELPER TO CLOSE ALL MODALS ---
            closeAllModals() {
                console.log('Closing all modals...');
                this.showForm = false;
                this.showEditModal = false;
                this.showKategoriModal = false;
                this.showImportModal = false;
            },

            // --- MODAL HANDLERS ---
            openKategoriModal() {
                 console.log("OPEN KATEGORI CLICKED");
                 this.closeAllModals();
                 this.$nextTick(() => { this.showKategoriModal = true; });
            },
            
            openImportModal() {
                console.log("OPEN IMPORT CLICKED");
                this.closeAllModals();
                this.$nextTick(() => { this.showImportModal = true; });
            },
            
            openBarangMasukModal() {
                console.log("OPEN BARANG MASUK CLICKED");
                this.closeAllModals();
                this.resetForm();
                this.$nextTick(() => { this.showForm = true; this.mode = 'new'; });
            },

            openEditModal(id) {
                console.log("OPEN EDIT CLICKED", id);
                this.closeAllModals();
                
                const barang = this.barangList.find(b => b.id_barang == id);
                if (!barang) return this.notify('error', 'Data hilang!');
                
                this.editItem = { ...this.editItem, ...barang, imagePreview: null, imageFile: null };
                
                this.$nextTick(() => { this.showEditModal = true; });
            },

            searchBarang() {
                if (!this.searchQuery.trim()) return this.filteredBarang = this.barangList;
                const query = this.searchQuery.toLowerCase();
                this.filteredBarang = this.barangList.filter(b => b.nama_barang.toLowerCase().includes(query) || b.kode_barang.toLowerCase().includes(query));
            },
            
            selectExistingBarang(barang) {
                this.selectedBarang = barang;
                this.existingItem.harga_beli_baru = barang.harga_beli;
            },

            generateRandomCode(target = 'new') {
                const random = '899' + Math.floor(Math.random() * 10000000000).toString().padStart(10, '0');
                if(target === 'new') this.newItem.kode_barang = random; else this.editItem.kode_barang = random;
            },

            async processBarangMasuk() {
                const formData = new FormData();
                formData.append('tanggal_masuk', this.form.tanggal_masuk);
                formData.append('keterangan', this.form.keterangan);
                
                if (this.mode === 'existing') {
                     if(!this.selectedBarang) return;
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
                if (this.newItem.gambar) formData.append('gambar', this.newItem.gambar);
                
                try {
                    const response = await fetch('{{ route("admin.stock.store") }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                        body: formData
                    });
                    const result = await response.json();
                    if (result.success) {
                        this.notify('success', result.message);
                        this.closeModal();
                        setTimeout(() => location.reload(), 1500); 
                    } else { this.notify('error', result.message); }
                } catch (error) { this.notify('error', error.message); }
            },
            
            async submitEditBarang() {
                try {
                    const formData = new FormData();
                    formData.append('_method', 'PUT');
                    for (const key in this.editItem) {
                        if (this.editItem[key] !== null) formData.append(key, this.editItem[key]);
                    }
                    if (this.editItem.imageFile) formData.append('gambar', this.editItem.imageFile);

                    const response = await fetch(`/admin/stock/${this.editItem.id_barang}`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                        body: formData
                    });
                    const result = await response.json();
                    if (result.success) {
                        this.notify('success', 'Update berhasil!');
                        this.showEditModal = false;
                        setTimeout(() => location.reload(), 1500);
                    } else { this.notify('error', result.message); }
                } catch (error) { this.notify('error', error.message); }
            },
            
            async hapusBarang(id, nama) {
                if (!confirm(`Hapus ${nama}?`)) return;
                try {
                    const response = await fetch(`/admin/stock/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } });
                    const result = await response.json();
                    if (result.success) {
                         this.notify('success', 'Dihapus!');
                         setTimeout(() => location.reload(), 1000);
                    }
                } catch (e) { this.notify('error', e.message); }
            },

            async submitKategori() {
                try {
                    const response = await fetch('{{ route("kategori.store") }}', {
                         method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' },
                         body: JSON.stringify(this.kategoriBaru)
                    });
                    const result = await response.json();
                    if (result.success) {
                        this.notify('success', 'Kategori OK!');
                        this.kategoriList.push(result.data);
                        this.kategoriBaru = { kode_kategori: '', nama_kategori: '', deskripsi: '' };
                        this.showKategoriModal = false; // Close modal
                    }
                } catch (e) { this.notify('error', e.message); }
            },

            async hapusKategori(id) {
                 if (!confirm('Hapus kategori?')) return;
                 try {
                     await fetch(`/kategori/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
                     this.kategoriList = this.kategoriList.filter(k => k.id_kategori != id);
                     this.notify('success', 'Kategori dihapus');
                 } catch (e) { this.notify('error', e.message); }
            },

            async submitImport() {
                if (!this.fileSelected) return;
                this.isImporting = true;
                const formData = new FormData();
                formData.append('file', this.fileSelected);
                try {
                    const response = await fetch('{{ route("admin.stock.import") }}', {
                        method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }, body: formData
                    });
                    const result = await response.json();
                    if (result.success) {
                        this.notify('success', result.message);
                        this.showImportModal = false;
                        setTimeout(() => location.reload(), 2000);
                    } else { this.notify('error', result.message); }
                } catch (e) { this.notify('error', e.message); }
                finally { this.isImporting = false; }
            },

            closeModal() { this.showForm = false; setTimeout(() => this.resetForm(), 300); },
            resetForm() {
                this.mode = 'existing'; this.selectedBarang = null; this.existingItem = { jumlah: 1, harga_beli_baru: '' };
                this.newItem = { kode_barang: '', nama_barang: '', id_kategori: '', harga_beli: '', harga_jual: '', stok_awal: 0, stok_minimal: 10, satuan: '', deskripsi: '' };
            },
            notify(type, message) { window.dispatchEvent(new CustomEvent('notify', { detail: { type, message } })); },
            
            tambahStokBarang(id) {
                this.showForm = true; this.mode = 'existing';
                const barang = this.barangList.find(b => b.id_barang == id);
                if(barang) setTimeout(() => this.selectExistingBarang(barang), 100);
            },
            
            filterTable() {
                const url = new URL(window.location.href);
                this.tableSearch ? url.searchParams.set('search', this.tableSearch) : url.searchParams.delete('search');
                this.tableCategory ? url.searchParams.set('kategori', this.tableCategory) : url.searchParams.delete('kategori');
                this.tableStatus ? url.searchParams.set('stok_status', this.tableStatus) : url.searchParams.delete('stok_status');
                url.searchParams.delete('page');
                window.location.href = url.toString();
            },
            
            formatNumber(num) { return new Intl.NumberFormat('id-ID').format(num); },
            previewImage(e) { const f = e.target.files[0]; if(f) { this.newItem.gambar = f; const r = new FileReader(); r.onload = ev => this.imagePreview = ev.target.result; r.readAsDataURL(f); } },
            previewEditImage(e) { const f = e.target.files[0]; if(f) { this.editItem.imageFile = f; const r = new FileReader(); r.onload = ev => this.editItem.imagePreview = ev.target.result; r.readAsDataURL(f); } },
            printBarcodeWithQty(c, n) { this.printBarcode(c, n, 10); },
            printBarcode(code, name, initialQty = 1) { 
                if (!code) return;
                const url = `https://bwipjs-api.metafloor.com/?bcid=code128&text=${code}&scale=2&includetext&textxalign=center`;
                const win = window.open('', '_blank', 'width=900,height=700');
                if(win){
                    win.document.write(`<html><head><title>${name}</title><style>@page{size:auto;margin:5mm}body{font-family:sans-serif;margin:0;padding:20px}.grid{display:grid;grid-template-columns:repeat(3,1fr);gap:10px}.item{border:1px dashed #ccc;padding:10px;text-align:center;height:120px;display:flex;flex-direction:column;align-items:center;justify-content:center}.img{max-height:50px}</style></head><body><div class="grid" id="g"></div><script>const q=${initialQty},c="${code}",n="${name}",u="${url}";let h='';for(let i=0;i<q;i++)h+=\`<div class="item"><b>\${n.substring(0,20)}</b><img src="\${u}" class="img"><small>\${c}</small></div>\`;document.getElementById('g').innerHTML=h;window.print();<\/script></body></html>`);
                    win.document.close();
                }
            }
        }));
    });
</script>
