<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'razon_social',
        'ruc',
        'direccion',
        'logo_path',
        'sol_user',
        'sol_pass',
        'cert_path',
        'client_id',
        'client_secret',
        'production',
        'user_id'
    ];

    //RELCION LA TBLA COMPANY CON USERS
    //Relacion 1 a 1
    public function user(){
        return $this->belongsTo(User::class);
    }
}
