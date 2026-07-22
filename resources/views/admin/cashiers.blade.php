@extends('layouts.admin')

@section('title', 'Manajemen Staf & Kasir')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-12" x-data="{
    modalOpen: false,
    editMode: false,
    editId: null,
    form: {
        name: '',
        username: '',
        role: 'Cashier',
        company_id: '{{ $company->id ?? '' }}'
    },
    openAdd() {
        this.editMode = false;
        this.editId = null;
        this.form.name = '';
        this.form.username = '';
        this.form.role = 'Cashier';
        this.modalOpen = true;
    },
    openEdit(c) {
        this.editMode = true;
        this.editId = c.id;
        this.form.name = c.name;
        this.form.username = c.username;
        this.form.role = c.role;
        this.modalOpen = true;
    }
}" @keydown.escape.window="modalOpen = false">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center pb-6 border-b border-zinc-900 gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                <span class="text-zinc-500 text-[10px] tracking-[0.25em] font-bold uppercase">STAFF MANAGEMENT</span>
            </div>
            <h2 class="text-3xl font-black tracking-wider text-white">MANAJEMEN KASIR & ADMIN</h2>
            <p class="text-zinc-500 text-xs mt-1">Kelola akses akun staf untuk operasional ShowDrive.</p>
        </div>
        <button @click="openAdd()" class="bg-luxury-gold hover:bg-luxury-goldHover text-black text-[10px] font-extrabold px-6 py-3 tracking-wider uppercase transition-all flex items-center gap-2">
            <i class="fa-solid fa-user-plus"></i> TAMBAH STAF
        </button>
    </div>


    {{-- Tabel Kasir --}}
    <div class="bg-zinc-950 border border-zinc-900 p-6 shadow-2xl relative">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-zinc-900/50 text-zinc-400 uppercase tracking-widest text-[10px] border-b border-zinc-900">
                    <tr>
                        <th class="p-4">NAMA LENGKAP</th>
                        <th class="p-4">USERNAME</th>
                        <th class="p-4">HAK AKSES</th>
                        <th class="p-4 text-right">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-900">
                    @forelse($cashiers as $c)
                        <tr class="hover:bg-zinc-900/30 transition-colors">
                            <td class="p-4 font-bold text-white tracking-wider flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-zinc-800 flex items-center justify-center text-luxury-gold font-bold border border-zinc-700">
                                    {{ substr($c->name, 0, 1) }}
                                </div>
                                {{ $c->name }}
                            </td>
                            <td class="p-4 text-zinc-400 font-mono text-[10px]">{{ $c->username }}</td>
                            <td class="p-4">
                                @if(in_array($c->role, ['Admin', 'Super Admin', 'Administrator']))
                                    <span class="px-3 py-1 bg-amber-950/60 border border-amber-900/50 text-amber-400 font-bold text-[9px] uppercase tracking-wider rounded-full">
                                        {{ $c->role }}
                                    </span>
                                @elseif(in_array($c->role, ['Head Cashier', 'Cashier']))
                                    <span class="px-3 py-1 bg-blue-950/60 border border-blue-900/50 text-blue-400 font-bold text-[9px] uppercase tracking-wider rounded-full">
                                        {{ $c->role }}
                                    </span>
                                @else
                                    <span class="px-3 py-1 bg-zinc-900 border border-zinc-800 text-zinc-400 font-bold text-[9px] uppercase tracking-wider rounded-full">
                                        {{ $c->role }}
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-right">
                                <div class="inline-flex gap-2">
                                    <button @click="openEdit({{ json_encode($c) }})"
                                            @if($c->role === 'Super Admin') disabled title="Super Admin tidak dapat diedit dari UI" @endif
                                            class="{{ $c->role === 'Super Admin' ? 'text-zinc-600 border-zinc-800 cursor-not-allowed bg-zinc-900' : 'text-luxury-gold hover:text-white border-luxury-gold/20 hover:border-luxury-gold bg-zinc-900/50 cursor-pointer' }} transition-colors py-1 px-3 border rounded text-[10px]">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </button>
                                    @if($c->role === 'Super Admin')
                                        {{-- Super Admin: tidak bisa dihapus dari UI --}}
                                        <button disabled
                                                title="Super Admin tidak dapat dihapus"
                                                class="text-zinc-600 border border-zinc-800 py-1 px-3 rounded text-[10px] bg-zinc-900 cursor-not-allowed">
                                            <i class="fa-solid fa-shield-halved"></i>
                                        </button>
                                    @elseif(Auth::id() == $c->id)
                                        {{-- Tidak bisa hapus akun sendiri --}}
                                        <button disabled
                                                title="Akun aktif saat ini"
                                                class="text-zinc-600 border border-zinc-800 py-1 px-3 rounded text-[10px] bg-zinc-900 cursor-not-allowed">
                                            <i class="fa-solid fa-user-shield"></i>
                                        </button>
                                    @elseif(Auth::user()->role !== 'Super Admin' && $c->role === 'Admin')
                                        {{-- Kasir/Admin biasa tidak bisa hapus Admin lain --}}
                                        <button disabled
                                                title="Hanya Super Admin yang dapat menghapus akun Admin"
                                                class="text-zinc-600 border border-zinc-800 py-1 px-3 rounded text-[10px] bg-zinc-900 cursor-not-allowed">
                                            <i class="fa-solid fa-lock"></i>
                                        </button>
                                    @else
                                        <form action="{{ route('admin.cashiers.destroy', $c->id) }}" method="POST" onsubmit="return confirm('Yakin menghapus akun staf ini? Aksi ini tidak dapat dibatalkan.')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-400 transition-colors py-1 px-3 border border-red-900/30 hover:border-red-500 rounded text-[10px] bg-red-950/20">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center text-zinc-500 italic text-xs">
                                <i class="fa-solid fa-users text-3xl mb-3 block text-zinc-800"></i>
                                Belum ada data staf terdaftar selain default.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Form Kasir --}}
    <div x-show="modalOpen" style="display: none;" class="fixed inset-0 bg-black/90 backdrop-blur-sm z-[100] flex items-center justify-center p-4">
        <div x-show="modalOpen" @click.stop
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             class="bg-zinc-950 border border-zinc-800 w-full max-w-lg shadow-2xl relative">

            <div class="px-6 py-4 border-b border-zinc-900 flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <div class="w-1 h-5 bg-luxury-gold"></div>
                    <h3 class="font-bold text-sm tracking-[0.2em] text-zinc-200 uppercase" x-text="editMode ? 'EDIT DATA STAF' : 'STAF BARU'"></h3>
                </div>
                <button @click="modalOpen = false" class="text-zinc-500 hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <form :action="editMode ? `/admin/cashiers/${editId}` : '{{ route('admin.cashiers.store') }}'" method="POST" class="p-6 space-y-5 text-xs">
                @csrf
                <input type="hidden" name="_method" :value="editMode ? 'PUT' : 'POST'">
                <input type="hidden" name="company_id" x-model="form.company_id">

                <div>
                    <label class="block text-zinc-500 font-bold uppercase tracking-widest mb-1.5 text-[10px]">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="name" x-model="form.name" placeholder="Nama staf" class="w-full bg-zinc-900 border border-zinc-800 text-white p-3 focus:border-luxury-gold focus:outline-none transition-colors" required>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-zinc-500 font-bold uppercase tracking-widest mb-1.5 text-[10px]">Username <span class="text-red-500">*</span></label>
                        <input type="text" name="username" x-model="form.username" placeholder="Username unik" class="w-full bg-zinc-900 border border-zinc-800 text-white p-3 focus:border-luxury-gold focus:outline-none transition-colors font-mono" required>
                    </div>
                    <div>
                        <label class="block text-zinc-500 font-bold uppercase tracking-widest mb-1.5 text-[10px]">Peran / Role <span class="text-red-500">*</span></label>
                        <select name="role" x-model="form.role" class="w-full bg-zinc-900 border border-zinc-800 text-white p-3 focus:border-luxury-gold focus:outline-none transition-colors" required>
                            <option value="Cashier">Cashier</option>
                            <option value="Admin">Administrator</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-zinc-500 font-bold uppercase tracking-widest mb-1.5 text-[10px]">Password <span x-show="!editMode" class="text-red-500">*</span></label>
                    <input type="password" name="password" placeholder="Minimal 8 karakter" class="w-full bg-zinc-900 border border-zinc-800 text-white p-3 focus:border-luxury-gold focus:outline-none transition-colors" :required="!editMode">
                    <p x-show="editMode" class="text-[9px] text-zinc-500 mt-1 italic">* Kosongkan jika tidak ingin mengubah password.</p>
                </div>

                <div class="flex gap-3 pt-4 border-t border-zinc-900 mt-6">
                    <button type="button" @click="modalOpen = false" class="flex-1 bg-zinc-900 hover:bg-zinc-800 text-zinc-400 font-bold py-3 tracking-widest uppercase transition-colors text-[10px]">BATAL</button>
                    <button type="submit" class="flex-[2] bg-luxury-gold hover:bg-luxury-goldHover text-black font-extrabold py-3 tracking-widest uppercase transition-all flex items-center justify-center gap-2 text-[10px]">
                        <i class="fa-solid fa-floppy-disk"></i> <span x-text="editMode ? 'UPDATE STAF' : 'SIMPAN STAF'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
