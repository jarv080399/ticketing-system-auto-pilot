<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
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
            'ticket_number' => $this->ticket_number,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'priority' => $this->priority,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'requester' => [
                'id' => $this->requester->id,
                'name' => $this->requester->name,
            ],
            'agent' => $this->agent ? [
                'id' => $this->agent->id,
                'name' => $this->agent->name,
                'email' => $this->agent->email,
            ] : null,
            'source' => $this->source,
            'tags' => $this->tags,
            'comments' => $this->whenLoaded('comments'),
            'attachments' => $this->whenLoaded('attachments', function () {
                return $this->attachments->map(fn ($a) => [
                    'id'        => $a->id,
                    'file_name' => $a->file_name,
                    'file_size' => $a->file_size,
                    'mime_type' => $a->mime_type,
                    'url'       => \Illuminate\Support\Facades\Storage::disk('public')->url($a->file_path),
                    'is_image'  => str_starts_with($a->mime_type ?? '', 'image/'),
                ]);
            }),
            'sla_due_at' => $this->sla_due_at,
            'first_response_at' => $this->first_response_at,
            'resolved_at' => $this->resolved_at,
            'closed_at' => $this->closed_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
