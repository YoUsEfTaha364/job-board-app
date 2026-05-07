<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Company extends Model
{    use HasFactory, Notifiable,HasUuids,SoftDeletes;
   protected $table="companies";

   protected $fillable = [
        'name',
        'address',
        'website',
        'industry',
        'owner_id',
    ];

    protected $keyType = 'string';
    public $incrementing = false;

      protected function casts(): array
    {
        return [
            'delated_at' => 'datetime'
        ];
    }

    public function owner(){
        return $this->belongsTo(User::class,"owner_id");
    }

    public function jobVacancies(){
        return $this->hasMany(JobVacancy::class);
    }

       public function jobs(){
        return $this->hasMany(JobVacancy::class);
    }
}
