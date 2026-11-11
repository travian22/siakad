<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DosenRequest;
use App\Models\Dosen;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class DosenController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();

        $dosens = Dosen::query()
            ->with('user')
            ->withCount('kelasPerkuliahan')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($subQuery) use ($search): void {
                    $subQuery->where('nip', 'like', '%'.$search.'%')
                        ->orWhere('nama_lengkap', 'like', '%'.$search.'%')
                        ->orWhereHas('user', fn ($userQuery) => $userQuery->where('username', 'like', '%'.$search.'%'));
                });
            })
            ->orderBy('nama_lengkap')
            ->paginate(10)
            ->withQueryString();

        return view('admin.dosen.index', [
            'dosens' => $dosens,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('admin.dosen.form', [
            'dosen' => new Dosen,
            'isEdit' => false,
        ]);
    }

    public function store(DosenRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated): void {
            $user = User::create([
                'username' => $validated['username'],
                'password' => Hash::make($validated['password']),
                'role' => 'dosen',
            ]);

            Dosen::create([
                'user_id' => $user->id,
                'nip' => $validated['nip'],
                'nama_lengkap' => $validated['nama_lengkap'],
                'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
                'telepon' => $validated['telepon'] ?? null,
                'alamat' => $validated['alamat'] ?? null,
            ]);
        });

        return redirect()
            ->route('admin.master-dosen.index')
            ->with('status', 'Data dosen dan akun login berhasil ditambahkan.');
    }

    public function edit(Dosen $dosen): View
    {
        $dosen->load('user');

        return view('admin.dosen.form', [
            'dosen' => $dosen,
            'isEdit' => true,
        ]);
    }

    public function update(DosenRequest $request, Dosen $dosen): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $dosen): void {
            $userData = [
                'username' => $validated['username'],
            ];

            if (! empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }

            $dosen->user->update($userData);

            $dosen->update([
                'nip' => $validated['nip'],
                'nama_lengkap' => $validated['nama_lengkap'],
                'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
                'telepon' => $validated['telepon'] ?? null,
                'alamat' => $validated['alamat'] ?? null,
            ]);
        });

        return redirect()
            ->route('admin.master-dosen.index')
            ->with('status', 'Data dosen berhasil diperbarui.');
    }

    public function destroy(Dosen $dosen): RedirectResponse
    {
        if ($dosen->kelasPerkuliahan()->exists()) {
            return back()->with('error', 'Dosen tidak dapat dihapus karena masih mengampu kelas perkuliahan.');
        }

        DB::transaction(function () use ($dosen): void {
            $user = $dosen->user;
            $dosen->delete();
            $user?->delete();
        });

        return redirect()
            ->route('admin.master-dosen.index')
            ->with('status', 'Data dosen berhasil dihapus.');
    }
}
