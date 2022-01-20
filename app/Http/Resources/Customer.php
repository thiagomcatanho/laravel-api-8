<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class Customer extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request): array
    {
        return [
           "id" => $this->id,
           "name" => $this->name,
           "email" => $this->email,
           "utr" => $this->utr,
           "phone" => $this->phone,
           "dob" => Carbon::parse($this->dob)->format('d/m/Y'),
           "profilePic" => asset('/').'uploads/'.$this->profile_pic_path,
           'created_at' => Carbon::parse($this->created_at)->format('d/m/Y H:i:s'),
           'updated_at' => Carbon::parse($this->updated_at)->format('d/m/Y H:i:s'),
        ];
    }
}
