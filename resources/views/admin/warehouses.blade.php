@extends('layouts.admin')

@section('title', 'Manajemen Gudang')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-12" x-data="{
    modalOpen: false,
    editMode: false,
    editId: null,
    form: {
        name: '',
        location: '',
        company_id: '{{ $company->id ?? '' }}'
    },
    openAdd() {
        this.editMode = false;
        this.editId = null;
        this.form.name = '';
        this.form.location = '';
        this.modalOpen = true;
    },
    openEdit(wh) {
        this.editMode = true;
        this.editId = wh.id;
        this.form.name = wh.name;
        this.form.location = wh.location;
        this.modalOpen = true;
    }
}" @keydown.escape.window="modalOpen = false">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center pb-6 border-b border-zinc-900 gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-zinc-500 text-[10px] tracking-[0.25em] font-bold uppercase">WAREHOUSE SYSTEM</span>
            </div>
            <h2 class="text-3xl font-black tracking-wider text-white">MANAJEMEN GUDANG</h2>
            <p class="text-zinc-500 text-xs mt-1">Kelola lokasi gudang penyimpanan kendaraan.</p>
        </div>
        <button @click="openAdd()" class="bg-luxury-gold hover:bg-luxury-goldHover text-black text-[10px] font-extrabold px-6 py-3 tracking-wider uppercase transition-all flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> TAMBAH GUDANG
        </button>
    </div>


    {{-- Tabel Gudang --}}
    <div class="bg-zinc-950 border border-zinc-900 p-6 shadow-2xl relative">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-zinc-900/50 text-zinc-400 uppercase tracking-widest text-[10px] border-b border-zinc-900">
                    <tr>
                        <th class="p-4">NAMA GUDANG</th>
                        <th class="p-4">LOKASI</th>
                        <th class="p-4 text-center">TOTAL UNIT</th>
                        <th class="p-4 text-right">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-900">
                    @forelse($warehouses as $wh)
                        <tr class="hover:bg-zinc-900/30 transition-colors">
                            <td class="p-4 font-bold text-white tracking-wider">{{ $wh->name }}</td>
                            <td class="p-4 text-zinc-400">{{ $wh->location }}</td>
                            <td class="p-4 text-center">
                                <span class="px-3 py-1 bg-zinc-900 border border-zinc-800 text-luxury-gold font-mono text-[10px] rounded-full">{{ $wh->items_count ?? 0 }} Unit</span>
                            </td>
                            <td class="p-4 text-right">
                                <div class="inline-flex gap-2">
                                    <button @click="openEdit({{ json_encode($wh) }})" class="text-luxury-gold hover:text-white transition-colors py-1 px-3 border border-luxury-gold/20 hover:border-luxury-gold rounded text-[10px] bg-zinc-900/50">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </button>
                                    <form action="{{ route('admin.warehouses.destroy', $wh->id) }}" method="POST" onsubmit="return confirm('Yakin menghapus gudang ini?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-400 transition-colors py-1 px-3 border border-red-900/30 hover:border-red-500 rounded text-[10px] bg-red-950/20">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center text-zinc-500 italic text-xs">
                                <i class="fa-solid fa-warehouse text-3xl mb-3 block text-zinc-800"></i>
                                Belum ada data gudang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Form Gudang --}}
    <div x-show="modalOpen" style="display: none;" class="fixed inset-0 bg-black/90 backdrop-blur-sm z-[100] flex items-center justify-center p-4">
        <div x-show="modalOpen" @click.stop
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             class="bg-zinc-950 border border-zinc-800 w-full max-w-lg shadow-2xl relative">

            <div class="px-6 py-4 border-b border-zinc-900 flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <div class="w-1 h-5 bg-luxury-gold"></div>
                    <h3 class="font-bold text-sm tracking-[0.2em] text-zinc-200 uppercase" x-text="editMode ? 'EDIT DATA GUDANG' : 'GUDANG BARU'"></h3>
                </div>
                <button @click="modalOpen = false" class="text-zinc-500 hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <form :action="editMode ? `/admin/warehouses/${editId}` : '{{ route('admin.warehouses.store') }}'" method="POST" class="p-6 space-y-5 text-xs">
                @csrf
                <input type="hidden" name="_method" :value="editMode ? 'PUT' : 'POST'">
                <input type="hidden" name="company_id" x-model="form.company_id">

                <div>
                    <label class="block text-zinc-500 font-bold uppercase tracking-widest mb-1.5 text-[10px]">Nama Gudang <span class="text-red-500">*</span></label>
                    <input type="text" name="name" x-model="form.name" placeholder="Contoh: Warehouse Jakarta Selatan" class="w-full bg-zinc-900 border border-zinc-800 text-white p-3 focus:border-luxury-gold focus:outline-none transition-colors" required>
                </div>

                <div>
                    <label class="block text-zinc-500 font-bold uppercase tracking-widest mb-1.5 text-[10px]">Lokasi / Alamat Lengkap <span class="text-red-500">*</span></label>
                    <textarea name="location" x-model="form.location" rows="3" placeholder="Alamat gudang" class="w-full bg-zinc-900 border border-zinc-800 text-white p-3 focus:border-luxury-gold focus:outline-none transition-colors" required></textarea>
                </div>

                <div class="flex gap-3 pt-4 border-t border-zinc-900 mt-6">
                    <button type="button" @click="modalOpen = false" class="flex-1 bg-zinc-900 hover:bg-zinc-800 text-zinc-400 font-bold py-3 tracking-widest uppercase transition-colors text-[10px]">BATAL</button>
                    <button type="submit" class="flex-[2] bg-luxury-gold hover:bg-luxury-goldHover text-black font-extrabold py-3 tracking-widest uppercase transition-all flex items-center justify-center gap-2 text-[10px]">
                        <i class="fa-solid fa-floppy-disk"></i> <span x-text="editMode ? 'UPDATE GUDANG' : 'SIMPAN GUDANG'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
