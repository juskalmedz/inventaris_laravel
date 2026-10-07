<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Matriks Hak Akses Default RBAC (1:1 dengan defaultRolePermissions React SPA)
     */
    public const ROLE_PERMISSIONS = [
        'Admin' => [
            'canViewDashboard' => true, 'canQuickActionDashboard' => true,
            'canViewInventaris' => true, 'canCreateInventaris' => true, 'canEditInventaris' => true, 'canDeleteInventaris' => true, 'canExportInventaris' => true,
            'canViewKaryawan' => true, 'canCreateKaryawan' => true, 'canEditKaryawan' => true, 'canDeleteKaryawan' => true,
            'canViewDepartemen' => true, 'canCreateDepartemen' => true, 'canEditDepartemen' => true, 'canDeleteDepartemen' => true, 'canExportDepartemen' => true,
            'canViewKategori' => true, 'canCreateKategori' => true, 'canEditKategori' => true, 'canDeleteKategori' => true,
            'canViewLokasi' => true, 'canCreateLokasi' => true, 'canEditLokasi' => true, 'canDeleteLokasi' => true,
            'canViewBarangMasuk' => true, 'canCreateBarangMasuk' => true, 'canEditBarangMasuk' => true, 'canDeleteBarangMasuk' => true,
            'canViewBarangKeluar' => true, 'canCreateBarangKeluar' => true, 'canEditBarangKeluar' => true, 'canDeleteBarangKeluar' => true,
            'canViewPeminjaman' => true, 'canCreatePeminjaman' => true, 'canEditPeminjaman' => true, 'canApprovePeminjaman' => true, 'canReturnPeminjaman' => true, 'canDeletePeminjaman' => true,
            'canViewMaintenance' => true, 'canCreateMaintenance' => true, 'canEditMaintenance' => true, 'canCompleteMaintenance' => true, 'canUnrepairMaintenance' => true, 'canDeleteMaintenance' => true,
            'canViewMutasi' => true, 'canCreateMutasi' => true, 'canEditMutasi' => true, 'canApproveMutasi' => true, 'canPrintMutasi' => true, 'canDeleteMutasi' => true,
            'canViewLaporan' => true, 'canExportExcel' => true, 'canPrintPdf' => true, 'canManageKop' => true,
            'canViewQr' => true, 'canPrintQr' => true,
            'canViewUsers' => true, 'canCreateUsers' => true, 'canEditUsers' => true, 'canResetPassword' => true, 'canDeleteUsers' => true,
            'canManageHakAkses' => true, 'canSwitchRoleSimulation' => true, 'canViewAuditLog' => true, 'canClearAuditLog' => true, 'canResetData' => true
        ],
        'Supervisor' => [
            'canViewDashboard' => true, 'canQuickActionDashboard' => true,
            'canViewInventaris' => true, 'canCreateInventaris' => true, 'canEditInventaris' => true, 'canDeleteInventaris' => false, 'canExportInventaris' => true,
            'canViewKaryawan' => true, 'canCreateKaryawan' => true, 'canEditKaryawan' => true, 'canDeleteKaryawan' => false,
            'canViewDepartemen' => true, 'canCreateDepartemen' => true, 'canEditDepartemen' => true, 'canDeleteDepartemen' => false, 'canExportDepartemen' => true,
            'canViewKategori' => true, 'canCreateKategori' => true, 'canEditKategori' => true, 'canDeleteKategori' => false,
            'canViewLokasi' => true, 'canCreateLokasi' => true, 'canEditLokasi' => true, 'canDeleteLokasi' => false,
            'canViewBarangMasuk' => true, 'canCreateBarangMasuk' => true, 'canEditBarangMasuk' => true, 'canDeleteBarangMasuk' => true,
            'canViewBarangKeluar' => true, 'canCreateBarangKeluar' => true, 'canEditBarangKeluar' => true, 'canDeleteBarangKeluar' => true,
            'canViewPeminjaman' => true, 'canCreatePeminjaman' => true, 'canEditPeminjaman' => true, 'canApprovePeminjaman' => true, 'canReturnPeminjaman' => true, 'canDeletePeminjaman' => true,
            'canViewMaintenance' => true, 'canCreateMaintenance' => true, 'canEditMaintenance' => true, 'canCompleteMaintenance' => true, 'canUnrepairMaintenance' => true, 'canDeleteMaintenance' => true,
            'canViewMutasi' => true, 'canCreateMutasi' => true, 'canEditMutasi' => true, 'canApproveMutasi' => true, 'canPrintMutasi' => true, 'canDeleteMutasi' => false,
            'canViewLaporan' => true, 'canExportExcel' => true, 'canPrintPdf' => true, 'canManageKop' => true,
            'canViewQr' => true, 'canPrintQr' => true,
            'canViewUsers' => true, 'canCreateUsers' => false, 'canEditUsers' => false, 'canResetPassword' => true, 'canDeleteUsers' => false,
            'canManageHakAkses' => false, 'canSwitchRoleSimulation' => false, 'canViewAuditLog' => true, 'canClearAuditLog' => false, 'canResetData' => false
        ],
        'Staff' => [
            'canViewDashboard' => true, 'canQuickActionDashboard' => true,
            'canViewInventaris' => true, 'canCreateInventaris' => true, 'canEditInventaris' => true, 'canDeleteInventaris' => false, 'canExportInventaris' => true,
            'canViewKaryawan' => true, 'canCreateKaryawan' => false, 'canEditKaryawan' => false, 'canDeleteKaryawan' => false,
            'canViewDepartemen' => true, 'canCreateDepartemen' => false, 'canEditDepartemen' => false, 'canDeleteDepartemen' => false, 'canExportDepartemen' => false,
            'canViewKategori' => true, 'canCreateKategori' => false, 'canEditKategori' => false, 'canDeleteKategori' => false,
            'canViewLokasi' => true, 'canCreateLokasi' => false, 'canEditLokasi' => false, 'canDeleteLokasi' => false,
            'canViewBarangMasuk' => true, 'canCreateBarangMasuk' => true, 'canEditBarangMasuk' => false, 'canDeleteBarangMasuk' => false,
            'canViewBarangKeluar' => true, 'canCreateBarangKeluar' => true, 'canEditBarangKeluar' => false, 'canDeleteBarangKeluar' => false,
            'canViewPeminjaman' => true, 'canCreatePeminjaman' => true, 'canEditPeminjaman' => false, 'canApprovePeminjaman' => false, 'canReturnPeminjaman' => true, 'canDeletePeminjaman' => false,
            'canViewMaintenance' => true, 'canCreateMaintenance' => true, 'canEditMaintenance' => false, 'canCompleteMaintenance' => false, 'canUnrepairMaintenance' => false, 'canDeleteMaintenance' => false,
            'canViewMutasi' => true, 'canCreateMutasi' => true, 'canEditMutasi' => true, 'canApproveMutasi' => false, 'canPrintMutasi' => true, 'canDeleteMutasi' => false,
            'canViewLaporan' => true, 'canExportExcel' => true, 'canPrintPdf' => false, 'canManageKop' => false,
            'canViewQr' => true, 'canPrintQr' => true,
            'canViewUsers' => false, 'canCreateUsers' => false, 'canEditUsers' => false, 'canResetPassword' => false, 'canDeleteUsers' => false,
            'canManageHakAkses' => false, 'canSwitchRoleSimulation' => false, 'canViewAuditLog' => false, 'canClearAuditLog' => false, 'canResetData' => false
        ],
        'Auditor' => [
            'canViewDashboard' => true, 'canQuickActionDashboard' => false,
            'canViewInventaris' => true, 'canCreateInventaris' => false, 'canEditInventaris' => false, 'canDeleteInventaris' => false, 'canExportInventaris' => true,
            'canViewKaryawan' => true, 'canCreateKaryawan' => false, 'canEditKaryawan' => false, 'canDeleteKaryawan' => false,
            'canViewDepartemen' => true, 'canCreateDepartemen' => false, 'canEditDepartemen' => false, 'canDeleteDepartemen' => false, 'canExportDepartemen' => true,
            'canViewKategori' => true, 'canCreateKategori' => false, 'canEditKategori' => false, 'canDeleteKategori' => false,
            'canViewLokasi' => true, 'canCreateLokasi' => false, 'canEditLokasi' => false, 'canDeleteLokasi' => false,
            'canViewBarangMasuk' => true, 'canCreateBarangMasuk' => false, 'canEditBarangMasuk' => false, 'canDeleteBarangMasuk' => false,
            'canViewBarangKeluar' => true, 'canCreateBarangKeluar' => false, 'canEditBarangKeluar' => false, 'canDeleteBarangKeluar' => false,
            'canViewPeminjaman' => true, 'canCreatePeminjaman' => false, 'canEditPeminjaman' => false, 'canApprovePeminjaman' => false, 'canReturnPeminjaman' => false, 'canDeletePeminjaman' => false,
            'canViewMaintenance' => true, 'canCreateMaintenance' => false, 'canEditMaintenance' => false, 'canCompleteMaintenance' => false, 'canUnrepairMaintenance' => false, 'canDeleteMaintenance' => false,
            'canViewMutasi' => true, 'canCreateMutasi' => false, 'canEditMutasi' => false, 'canApproveMutasi' => false, 'canPrintMutasi' => true, 'canDeleteMutasi' => false,
            'canViewLaporan' => true, 'canExportExcel' => true, 'canPrintPdf' => true, 'canManageKop' => false,
            'canViewQr' => true, 'canPrintQr' => false,
            'canViewUsers' => false, 'canCreateUsers' => false, 'canEditUsers' => false, 'canResetPassword' => false, 'canDeleteUsers' => false,
            'canManageHakAkses' => false, 'canSwitchRoleSimulation' => false, 'canViewAuditLog' => true, 'canClearAuditLog' => false, 'canResetData' => false
        ]
    ];

    protected $fillable = [
        'kode_user',
        'name',
        'username',
        'email',
        'password',
        'role',
        'status',
        'color_scheme',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isActive(): bool
    {
        return $this->status === 'Aktif';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'Admin';
    }

    public function isSupervisor(): bool
    {
        return $this->role === 'Supervisor';
    }

    public function isStaff(): bool
    {
        return $this->role === 'Staff';
    }

    public function isAuditor(): bool
    {
        return $this->role === 'Auditor';
    }

    public function hasPermission(string $permKey): bool
    {
        $rolePerms = self::ROLE_PERMISSIONS[$this->role] ?? [];
        return $rolePerms[$permKey] ?? false;
    }

    public function getPermissions(): array
    {
        return self::ROLE_PERMISSIONS[$this->role] ?? [];
    }

    public function toUserAccountArray(): array
    {
        return [
            'id' => $this->id,
            'kode_user' => $this->kode_user,
            'nama' => $this->name,
            'username' => $this->username,
            'role' => $this->role,
            'status' => $this->status,
            'color_scheme' => $this->color_scheme ?? 'rose',
        ];
    }

    public function mutasiRequests()
    {
        return $this->hasMany(Mutasi::class, 'created_by_user_id');
    }
}
