<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobVacancyResource extends JsonResource
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
        'title' => $this->title,
        'description' => $this->description,
        'location' => $this->location,
        'salary' => $this->salary,
        'type' => $this->type,
        'created_at' => $this->created_at,
        'updated_at' => $this->updated_at,

        'company' => new CompanyResource($this->whenLoaded('company')),
        'category' => new CategoryResource($this->whenLoaded('jobCategory')),
    ];
}
}
