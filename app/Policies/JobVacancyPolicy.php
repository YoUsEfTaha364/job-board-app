<?php

namespace App\Policies;

use App\Models\JobVacancy;
use App\Models\User;

class JobVacancyPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function show(User $user ,JobVacancy $jobvacancy){
        $company=$user->company;

       return  $company->jobVacancies()->where("id",$jobvacancy->id)->exists();

        
    }
    public function restore(User $user ,JobVacancy $jobvacancy){
        $company=$user->company;

       return  $company->jobVacancies()->withTrashed()->where("id",$jobvacancy->id)->exists();

        
    }
}
