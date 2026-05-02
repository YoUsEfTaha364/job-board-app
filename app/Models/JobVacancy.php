<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class JobVacancy extends Model
{
    

   use HasFactory, Notifiable,HasUuids,SoftDeletes;
   protected $table="job_vacancies";

   protected $fillable = [
        'title',
        'description',
        'location',
        'salary',
        'type',
        'company_id',
        'category_id',
    ];

    protected $keyType = 'string';
    public $incrementing = false;

      protected function casts(): array
    {
        return [
            'delated_at' => 'datetime'
        ];
    }

      public function jobApplications(){
        return $this->hasMany(JobApplication::class);
    }

     public function company(){
        return $this->belongsTo(Company::class);
    }
     public function jobCategory(){
        return $this->belongsTo(Company::class);
    }

}
