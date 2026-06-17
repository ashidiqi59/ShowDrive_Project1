@extends('layouts.app')

@section('title', 'Admin Portal - ShowDrive')

@section('content')
<div class="max-w-md mx-auto px-6 py-24 no-print">
    <div class="bg-zinc-950 border border-zinc-900 p-8 shadow-xl">
        <div class="text-center mb-8">
            <span class="text-luxury-gold font-bold tracking-[0.3em] text-[10px] uppercase block mb-1">OPERASIONAL INTERNAL</span>
            <h3 class="text-2xl font-black tracking-[0.2em]">ADMIN PORTAL</h3>
            <div class="w-12 h-[2px] bg-luxury-gold mx-auto mt-3"></div>
        </div>
        
        <form action="{{ route('login') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-zinc-500 text-[10px] font-bold tracking-wider uppercase mb-2">Email Admin</label>
                <input type="email" name="email" value="admin@showdrive.com" class="w-full bg-zinc-900/50 border border-zinc-800 focus:border-luxury-gold focus:ring-1 focus:ring-luxury-gold text-white p-3 text-xs focus:outline-none" required>
            </div>
            <div>
                <label class="block text-zinc-500 text-[10px] font-bold tracking-wider uppercase mb-2">Kata Sandi</label>
                <input type="password" name="password" value="admin" class="w-full bg-zinc-900/50 border border-zinc-800 focus:border-luxury-gold focus:ring-1 focus:ring-luxury-gold text-white p-3 text-xs focus:outline-none" required>
            </div>
            
            <div class="bg-zinc-900/40 p-3 border border-zinc-800 text-[10px] text-zinc-500 leading-normal">
                <i class="fa-solid fa-circle-info text-luxury-gold mr-1"></i> Gunakan email default <strong>admin@showdrive.com</strong> dan sandi <strong>admin</strong> untuk masuk.
            </div>

            <button type="submit" class="w-full bg-luxury-gold hover:bg-luxury-goldHover text-black font-extrabold py-3.5 text-xs tracking-[0.2em] uppercase transition-all">
                LOG IN SYSTEM
            </button>
        </form>
    </div>
</div>
@endsection
