<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleCrudTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        return User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.dev',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'roles' => ['admin'],
        ]);
    }

    private function createDosen(): User
    {
        return User::create([
            'name' => 'Dosen User',
            'email' => 'dosen@test.dev',
            'password' => bcrypt('password'),
            'role' => 'dosen',
            'roles' => ['dosen'],
        ]);
    }

    public function test_admin_dapat_menambah_role_baru(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post(route('roles.store'), [
            'nama' => 'Dosen Pembina',
            'kode' => 'dosen_pembina',
            'deskripsi' => 'Dosen pembina kemahasiswaan',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('roles', [
            'nama' => 'Dosen Pembina',
            'kode' => 'dosen_pembina',
            'is_system' => false,
        ]);
    }

    public function test_admin_dapat_mengupdate_role(): void
    {
        $admin = $this->createAdmin();

        $role = Role::create([
            'nama' => 'Staff Prodi',
            'kode' => 'staff_prodi',
            'deskripsi' => 'Deskripsi lama',
            'is_system' => false,
        ]);

        $response = $this->actingAs($admin)->patch(route('roles.update', $role->id), [
            'nama' => 'Staff Administrasi Prodi',
            'kode' => 'staff_adm_prodi',
            'deskripsi' => 'Deskripsi baru',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'nama' => 'Staff Administrasi Prodi',
            'kode' => 'staff_adm_prodi',
        ]);
    }

    public function test_admin_dapat_menghapus_role(): void
    {
        $admin = $this->createAdmin();

        $role = Role::create([
            'nama' => 'Asisten Lab',
            'kode' => 'asisten_lab',
        ]);

        $response = $this->actingAs($admin)->delete(route('roles.destroy', $role->id));

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }

    public function test_admin_dapat_mengakses_halaman_index_create_dan_edit_role(): void
    {
        $admin = $this->createAdmin();
        $role = Role::create([
            'nama' => 'Staff Prodi',
            'kode' => 'staff_prodi',
        ]);

        $this->actingAs($admin)->get(route('roles.index'))->assertOk();
        $this->actingAs($admin)->get(route('roles.create'))->assertOk();
        $this->actingAs($admin)->get(route('roles.edit', $role->id))->assertOk();
    }

    public function test_non_admin_ditolak_mengakses_crud_role(): void
    {
        $dosen = $this->createDosen();

        $this->actingAs($dosen)
            ->get(route('roles.index'))
            ->assertForbidden();

        $this->actingAs($dosen)
            ->post(route('roles.store'), [
                'nama' => 'Role Hacking',
                'kode' => 'role_hacking',
            ])
            ->assertForbidden();
    }
}