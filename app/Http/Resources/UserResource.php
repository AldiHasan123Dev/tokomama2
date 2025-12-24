<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Hash;


class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'id_user' => $this->id,
            'name_user' => $this->name ?? '-',
             'password' => $this->password ? Hash::make($this->password) : '-', // atau null
            'email' => $this->email ?? '-',
            'phone' => $this->phone ?? '-',
            'address' => $this->address ?? '-',
            'id_role' => $this->role->id ?? '-',
            'name_role' => $this->role->name ?? '-'
        ];
    }
}
