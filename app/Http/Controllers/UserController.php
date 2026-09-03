<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(10);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'roles' => 'nullable|array|min:1',
            'roles.*' => 'string|max:50',
            'role' => 'nullable|string|max:50',
        ]);

        $roles = $request->roles ?? ($request->role ? (array) $request->role : ['dosen']);
        $roles = array_values(array_unique(array_map('strtolower', $roles)));

        $primaryRole = in_array('admin', $roles) ? 'admin' : (in_array('dosen', $roles) ? 'dosen' : (in_array('direktur', $roles) ? 'direktur' : $roles[0]));

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'role' => $primaryRole,
            'roles' => $roles,
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'roles' => 'nullable|array|min:1',
            'roles.*' => 'string|max:50',
            'role' => 'nullable|string|max:50',
        ]);

        $roles = $request->roles ?? ($request->role ? (array) $request->role : $user->getRolesList());
        $roles = array_values(array_unique(array_map('strtolower', $roles)));

        $primaryRole = in_array('admin', $roles) ? 'admin' : (in_array('dosen', $roles) ? 'dosen' : (in_array('direktur', $roles) ? 'direktur' : $roles[0]));

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $primaryRole,
            'roles' => $roles,
        ];

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8']);
            $data['password'] = $request->password;
        }

        $user->update($data);

        if ($user->dosen) {
            $jabatan = 'Dosen';
            foreach ($roles as $r) {
                if ($r !== 'dosen' && $r !== 'admin' && $r !== 'mahasiswa') {
                    $roleModel = \App\Models\Role::where('kode', $r)->first();
                    $jabatan = $roleModel ? $roleModel->nama : ucfirst($r);
                    break;
                }
            }
            $user->dosen->update(['jabatan' => $jabatan]);
        }

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}
