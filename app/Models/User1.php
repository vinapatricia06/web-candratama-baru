<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User1 extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles; // Pastikan HasRoles sudah ada di sini


    protected $table = 'users1'; 
    protected $primaryKey = 'id_user'; 
    protected $fillable = [
        'nama', 'username', 'password', 'role',
    ];
    protected $hidden = [
        'password',
    ];

    public function progressProjects()
    {
        return $this->hasMany(ProgressProject::class, 'teknisi_id', 'id_user');
    }

    public function hasRole($role)
    {
        return $this->role === $role;
    }

    public function isSuperADM() { return $this->role === 'superadmin'; }
    public function isAdmin() { return $this->role === 'admin'; }
    public function isCEO() { return $this->role === 'CEO'; }
    public function isDM() { return $this->role === 'marketing'; }
    public function isIC() { return $this->role === 'interior_consultan'; }
    public function isWRH() { return $this->role === 'warehouse'; }
    public function isPCH() { return $this->role === 'purchasing'; }
    public function isEks() { return $this->role === 'ekspedisi'; }
    public function isCS() { return $this->role === 'cleaning_services'; }
    public function isTeknisi() { return $this->role === 'teknisi'; }
    public function isFNC() { return $this->role === 'finance'; }
}
