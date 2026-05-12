<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminJobApplicationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'ai_generated_score' => $this->ai_generated_score,
            'ai_generated_feedback' => $this->ai_generated_feedback,
            
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'job_vacancy' =>new JobVacancyResource($this->whenLoaded("jobVacancy")) ,
            'user' =>new UserResource($this->whenLoaded("user")) ,
            'resume' =>new ResumeResource($this->whenLoaded("resume")) ,
        ];
    }
}
