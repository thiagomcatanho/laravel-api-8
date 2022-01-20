<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class Income extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {       
        return [
           "id" => $this->id,
           "description" => $this->description,
           "customer_id" => $this->customer_id,
           "amount" => $this->amount,
           "taxYear" => $this->tax_year,
           "incomeDate" => Carbon::parse($this->income_date)->format('d/m/Y H:i:s'),
           "incomeFile" => asset('/').'uploads/'.$this->income_file_path,
           'createdAt' => Carbon::parse($this->created_at)->format('d/m/Y H:i:s'),
           'updatedAt' => Carbon::parse($this->updated_at)->format('d/m/Y H:i:s')
        ];
    }
}
