<div class="-mt-4 grid grid-cols-1 sm:grid-cols-[max-content_1fr] gap-1 sm:gap-x-1 items-start text-sm"">
    <!-- Kolom 1: Label -->
    <span class="font-medium text-gray-750 dark:text-gray-200 min-w-[80px]">
        Terbilang:
    </span>
    
    <!-- Kolom 2: Isi Teks Angka Terbilang -->
    <span class="text-gray-600 dark:text-gray-400 breakdown-words italic text-green-700">
        {{-- Mengambil value dinamis dari state komponen --}}
        {{ $getState() }}
    </span>
</div>