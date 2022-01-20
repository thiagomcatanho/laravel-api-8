<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerListReport extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request): array
    {
        $customers = [];
        $customersWithIncome = [];

        foreach ($this->resource as $value):
            $customers[$value->id][] = $value;
        endforeach;

        foreach ($customers as $customer):
            $customersWithIncome[] = [
                "id" => $customer[0]->id,
                "name" => $customer[0]->name,
                "email" => $customer[0]->email,
                "total" => number_format($customer[0]->total, 2),
                "incomes" => $this->mapIncomes($customer)
            ];
        endforeach;

        return $customersWithIncome;
    }

    private function mapIncomes($incomes): array
    {
        
        if (!empty($incomes) && $incomes[0]->description):
            return array_map(function ($income){
                return [
                    "description" => $income->description,
                    "amount" => number_format($income->amount, 2),
                    "incomeDate" => $income->incomeDate
                ];
            }, $incomes);

        else: 
            return [];
        endif;
    }
}
