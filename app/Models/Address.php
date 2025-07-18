<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_contact',
        'zip_code',
        'country',
        'state',
        'street_address',
        'address_number',
        'city',
        'address_line',
        'neighborhood',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'id_contact');
    }
}
