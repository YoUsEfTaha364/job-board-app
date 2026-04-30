<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class JobCategory extends Model
{
    
   use HasFactory, Notifiable,HasUuids,SoftDeletes;
   protected $table="job_categories";

   protected $fillable = [
        'name'
    ];

    protected $keyType = 'string';
    public $incrementing = false;

      protected function casts(): array
    {
        return [
            'delated_at' => 'datetime'
        ];
    }

      public function jobVacancies(){
        return $this->hasMany(JobVacancy::class);
    }

}
