<?php

namespace App\Http\Controllers\MobilCinta\Admin;

use App\Http\Controllers\Controller;
use App\Models\MobilCinta\Account;
use App\Models\MobilCinta\Role;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AccountController extends Controller
{
    /**
     * Daftar akun Mobil Cinta.
     */
    public function index(Request $request): View
    {
        $query = Account::with('role')->orderBy('nama');

        $search = trim((string) $request->query('q', ''));
        $roleId = $request->query('peran_id');
        $status = $request->query('status');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nama_pengguna', 'like', "%{$search}%");
            });
        }

        if ($request->filled('peran_id')) {
            $query->where('peran_id', $roleId);
        }

        if (in_array((string) $status, ['0', '1'], true)) {
            $query->where('status_aktif', (int) $status);
        }

        $accounts = $query->paginate(5)->withQueryString();
        $roles = Role::orderBy('nama_peran')->get();

        $totalAccounts = Account::count();
        $activeAccounts = Account::where('status_aktif', true)->count();

        return view('mobil-cinta.admin.akun.index', compact(
            'accounts',
            'roles',
            'search',
            'roleId',
            'status',
            'totalAccounts',
            'activeAccounts'
        ));
    }

    /**
     * Mengambil saran akun untuk autocomplete pencarian.
     */
    public function suggestions(Request $request): JsonResponse
    {
        $search = trim((string) $request->query('q', ''));

        if ($search === '') {
            return response()->json([]);
        }

        $accounts = Account::query()
            ->where(function ($query) use ($search) {
                $query->where('nama', 'like', "%{$search}%")
                    ->orWhere('nama_pengguna', 'like', "%{$search}%");
            })
            ->orderBy('nama')
            ->limit(8)
            ->get([
                'id',
                'nama',
                'nama_pengguna',
            ]);

        return response()->json($accounts);
    }

    /**
     * Form tambah akun.
     */
    public function create(): View
    {
        $roles = Role::orderBy('nama_peran')->get();

        return view('mobil-cinta.admin.akun.create', compact('roles'));
    }

    /**
     * Simpan akun baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nama_pengguna' => [
                'required',
                'string',
                'max:255',
                'unique:mobil_cinta_akun,nama_pengguna',
            ],
            'peran_id' => ['required', 'integer', 'exists:mobil_cinta_peran,id'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'status_aktif' => ['nullable', 'boolean'],
        ]);

        Account::create([
            'nama' => $validated['nama'],
            'nama_pengguna' => $validated['nama_pengguna'],
            'peran_id' => $validated['peran_id'],
            'kata_sandi' => $validated['password'],
            'status_aktif' => $request->boolean('status_aktif', true),
        ]);

        return redirect()
            ->route('mobil-cinta.admin.akun.index')
            ->with('status', 'Akun Mobil Cinta berhasil ditambahkan.');
    }

    /**
     * Form edit akun.
     */
    public function edit(Account $account): View
    {
        $roles = Role::orderBy('nama_peran')->get();

        return view('mobil-cinta.admin.akun.edit', compact(
            'account',
            'roles'
        ));
    }

    /**
     * Perbarui akun.
     */
    public function update(Request $request, Account $account): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nama_pengguna' => [
                'required',
                'string',
                'max:255',
                Rule::unique('mobil_cinta_akun', 'nama_pengguna')
                    ->ignore($account->id),
            ],
            'peran_id' => ['required', 'integer', 'exists:mobil_cinta_peran,id'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'status_aktif' => ['sometimes', 'boolean'],
        ]);

        $currentAccount = Auth::guard('mobil_cinta')->user();

        // Admin tidak boleh mengubah perannya sendiri.
        if (
            $currentAccount &&
            (int) $currentAccount->id === (int) $account->id &&
            (int) $validated['peran_id'] !== (int) $account->peran_id
        ) {
            return back()
                ->withErrors(['peran_id' => 'Kamu tidak dapat mengubah peran akun sendiri.'])
                ->withInput();
        }

        $account->nama = $validated['nama'];
        $account->nama_pengguna = $validated['nama_pengguna'];
        $account->peran_id = $validated['peran_id'];

        if (!empty($validated['password'])) {
            $account->kata_sandi = $validated['password'];
        }

        if (array_key_exists('status_aktif', $validated)) {
            $newStatus = (bool) $validated['status_aktif'];

            if (!$newStatus) {
                $this->ensureAccountCanBeDeactivated($account);
            }

            $account->status_aktif = $newStatus;
        }

        $account->save();

        return redirect()
            ->route('mobil-cinta.admin.akun.index')
            ->with('status', 'Data akun berhasil diperbarui.');
    }

    /**
     * Aktifkan atau nonaktifkan akun.
     */
    public function toggleStatus(Account $account): RedirectResponse
    {
        if ($account->status_aktif) {
            $this->ensureAccountCanBeDeactivated($account);
            $account->status_aktif = false;
        } else {
            $account->status_aktif = true;
        }

        $account->save();

        return back()->with(
            'status',
            $account->status_aktif
                ? 'Akun berhasil diaktifkan.'
                : 'Akun berhasil dinonaktifkan.'
        );
    }

    /**
     * Reset password akun.
     */
    public function resetPassword(
        Request $request,
        Account $account
    ): RedirectResponse {
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $account->kata_sandi = $validated['password'];
        $account->save();

        return back()->with('status', 'Password akun berhasil direset.');
    }

    /**
     * Mencegah admin menonaktifkan akun sendiri
     * atau menonaktifkan satu-satunya admin aktif.
     */
    private function ensureAccountCanBeDeactivated(Account $account): void
    {
        $currentAccount = Auth::guard('mobil_cinta')->user();

        if (
            $currentAccount &&
            (int) $currentAccount->id === (int) $account->id
        ) {
            abort(403, 'Kamu tidak dapat menonaktifkan akun sendiri.');
        }

        if ($account->role?->nama_peran === 'admin') {
            $activeAdmins = Account::where('status_aktif', true)
                ->whereHas('role', function ($query) {
                    $query->where('nama_peran', 'admin');
                })
                ->count();

            if ($activeAdmins <= 1) {
                abort(403, 'Tidak dapat menonaktifkan satu-satunya akun Admin aktif.');
            }
        }
    }
}
