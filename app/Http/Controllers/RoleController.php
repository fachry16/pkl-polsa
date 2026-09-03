<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::orderBy('id')->paginate(10);

        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        return view('roles.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'kode' => 'required|string|max:50|alpha_dash|unique:roles,kode',
            'deskripsi' => 'nullable|string|max:255',
        ]);

        $validated['kode'] = Str::slug($validated['kode'], '_');

        Role::create([
            'nama' => $validated['nama'],
            'kode' => $validated['kode'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'is_system' => false,
        ]);

        return redirect()->route('roles.index')->with('success', 'Role/Jabatan baru berhasil ditambahkan.');
    }

    public function edit(Role $role)
    {
        return view('roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'kode' => 'required|string|max:50|alpha_dash|unique:roles,kode,' . $role->id,
            'deskripsi' => 'nullable|string|max:255',
        ]);

        $role->update([
            'nama' => $validated['nama'],
            'kode' => Str::slug($validated['kode'], '_'),
            'deskripsi' => $validated['deskripsi'] ?? null,
        ]);

        return redirect()->route('roles.index')->with('success', 'Role/Jabatan berhasil diperbarui.');
    }

    public function destroy(Role $role)
    {
        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role/Jabatan berhasil dihapus.');
    }
}