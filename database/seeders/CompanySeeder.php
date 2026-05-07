<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\User;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $owners = User::where('role', 'company-owner')->get();

        if ($owners->count() > 0) {
            Company::create([
                'address' => '123 Tech Lane',
                'website' => 'https://techcorp.example.com',
                'industry' => 'IT Services',
                'owner_id' => $owners[0]->id,
            ]);
        }

        if ($owners->count() > 1) {
            Company::create([
                'address' => '456 Business Blvd',
                'website' => 'https://businessinc.example.com',
                'industry' => 'Finance',
                'owner_id' => $owners[1]->id,
            ]);
        }
    }
}
