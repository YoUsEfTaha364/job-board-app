<?php

namespace App\Policies;

use App\Models\JobApplication;
use App\Models\JobVacancy;
use App\Models\User;

class JobApplicationPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    // public function show(User $user ,JobVacancy $jobvacancy){
    //     $company=$user->company;

    //    return  $company->jobVacancies()->where("id",$jobvacancy->id)->exists();


    // }
    public function companyRestore(User $user, JobApplication $jobapplication)
    {

        $company = $user->company;

        $jobVacancy = $jobapplication->jobVacancy;


        return $jobVacancy->company_id == $company->id && $jobapplication->admin_archived == false;
    }
    public function seekerRestore(User $user, JobApplication $jobapplication)
    {

        $company = $user->company;

        $jobVacancy = $jobapplication->jobVacancy;
        return $this->seekerActions($user, $jobapplication) && $jobapplication->admin_archived == false;
    }
    public function seekerActions(User $user, JobApplication $jobapplication)
    {
        return $jobapplication->user_id == $user->id;
    }
}
