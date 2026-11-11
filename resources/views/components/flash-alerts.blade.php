@if (session('status'))
    <div class="mb-5 rounded-lg border border-emerald-700 bg-emerald-50 px-4 py-3 text-sm text-emerald-900" role="status">
        {{ session('status') }}
    </div>
@endif

@if (session('warning'))
    <div class="mb-5 rounded-lg border border-amber-700 bg-amber-50 px-4 py-3 text-sm text-amber-900" role="alert">
        {{ session('warning') }}
    </div>
@endif

@if (session('error'))
    <div class="mb-5 rounded-lg border border-red-700 bg-red-50 px-4 py-3 text-sm text-red-900" role="alert">
        {{ session('error') }}
    </div>
@endif

@if ($errors->any())
    <div class="mb-5 rounded-lg border border-red-700 bg-red-50 px-4 py-3 text-sm text-red-900" role="alert">
        <p class="font-semibold">Terdapat kesalahan pada data yang dikirim:</p>
        <ul class="mt-1.5 list-disc list-inside flex flex-col gap-0.5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

