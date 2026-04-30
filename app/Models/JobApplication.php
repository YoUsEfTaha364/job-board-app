<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class JobApplication extends Model
{
    use HasFactory, Notifiable,HasUuids,SoftDeletes;
   protected $table="job_applications";

   protected $fillable = [
        'status',
        'aiGeneratedScore',
        'aiGeneratedFeedback',
        'jobVacancyId',
        'resumeId',
        'userId',
        
    ];

    protected $keyType = 'string';
    public $incrementing = false;

      protected function casts(): array
    {
        return [
            'delated_at' => 'datetime'
        ];
    }

       public function jobVacancy(){
        return $this->belongsTo(JobVacancy::class);
    }
       public function user(){
        return $this->belongsTo(User::class);
    }
       public function resume(){
        return $this->belongsTo(Resume::class);
    }
}
