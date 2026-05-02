<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Resume extends Model
{
     use HasFactory, Notifiable,HasUuids,SoftDeletes;
   protected $table="resumes";

   protected $fillable = [
        'file_name',
        'file_url',
        'contract_details',
        'skills',
        'summary',
        'experience',
        'education',
        'user_id',
        
    ];

    protected $keyType = 'string';
    public $incrementing = false;

      protected function casts(): array
    {
        return [
            'delated_at' => 'datetime'
        ];
    }

       public function user(){
        return $this->belongsTo(User::class);
    }

     public function jobApplications(){
        return $this->hasMany(JobApplication::class);
    }

}
