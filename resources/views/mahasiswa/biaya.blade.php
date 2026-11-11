<x-layouts.app title="Informasi Biaya Kuliah">
    <x-page-header
        title="Biaya Kuliah"
        subtitle="Pantau rincian tagihan dan status pembayaran kuliah Anda di setiap periode akademik."
    />

    <section aria-label="Ringkasan Pembayaran" class="grid grid-cols-1 gap-4 sm:grid-cols-2 mb-6">
        <div class="rounded-lg border border-slate-300 bg-white p-5">
            <p class="text-sm font-medium text-slate-600">Total Tagihan Belum Bayar</p>
            <p class="mt-2 text-2xl font-semibold text-amber-900">
                Rp {{ number_format($totalBelumBayar, 0, ',', '.') }}
            </p>
        </div>
        <div class="rounded-lg border border-slate-300 bg-white p-5">
            <p class="text-sm font-medium text-slate-600">Total Sudah Lunas</p>
            <p class="mt-2 text-2xl font-semibold text-emerald-900">
                Rp {{ number_format($totalLunas, 0, ',', '.') }}
            </p>
        </div>
    </section>

    <div class="rounded-lg border border-slate-300 bg-white">
        @if ($tagihans->isEmpty())
            <div class="p-8 text-center">
                <p class="text-sm text-slate-700">Belum ada tagihan biaya kuliah yang tercatat untuk akun Anda.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-700">
                        <tr>
                            <th scope="col" class="px-4 py-3">Periode Akademik</th>
                            <th scope="col" class="px-4 py-3">Nominal Tagihan</th>
                            <th scope="col" class="px-4 py-3">Status Pembayaran</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @foreach ($tagihans as $biaya)
                            <tr>
                                <td class="px-4 py-3 font-medium text-slate-900">
                                    {{ $biaya->periodeAkademik->nama_periode }}
                                    @if ($biaya->periodeAkademik->is_active)
                                        <span class="ml-1 inline-flex items-center rounded-md border border-slate-400 bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-800">
                                            Aktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-semibold text-slate-900">
                                    Rp {{ number_format((float) $biaya->nominal_tagihan, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3">
                                    @if ($biaya->status_bayar === 'lunas')
                                        <span class="inline-flex items-center rounded-md border border-emerald-700 bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-900">
                                            Lunas
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-md border border-amber-700 bg-amber-50 px-2.5 py-0.5 text-xs font-semibold text-amber-900">
                                            Belum Bayar
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-layouts.app>

