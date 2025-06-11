<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles'; // nama tabel
    protected $primaryKey = 'role_id'; // primary key bukan 'id'

    public $timestamps = false; // jika tidak ada created_at/updated_at
}
