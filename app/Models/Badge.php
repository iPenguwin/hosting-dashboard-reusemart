<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Badge extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'badges';
    protected $primaryKey = 'ID_BADGE';
    public $timestamps = false;

    protected $fillable = [
        'ID_PENITIP',
        'NAMA_BADGE',
        'START_DATE',
        'END_DATE',
    ];

    public function penitip()
    {
        return $this->belongsTo(Penitip::class, 'ID_PENITIP', 'ID_PENITIP');
    }
}
