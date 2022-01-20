<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController as BaseController;
use App\Http\Resources\Income as IncomeResource;
use Illuminate\Http\Request;
use App\Models\Income;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class IncomeController extends BaseController
{
    /**
     * Display a listing of the resource.
     * 
     * @return \Illuminate\Http\JsonResponse
     */

    public function index(): JsonResponse
    {
        try {
            $incomes = Income::all();

            return $this->sendResponse(IncomeResource::collection($incomes), 'Incomes retrieved successfully.');

        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), [], 422);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */

    public function show($id): JsonResponse
    {
        try {
            $income = Income::find($id);

            if (!$income):
                return $this->sendError('Income not found');
            endif;

            return $this->sendResponse(new IncomeResource($income), 'Income retrieved successfully.');

        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), [], 422);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function store(Request $request): JsonResponse
    {   
        DB::beginTransaction();

        try {
            $input = $request->all();

            $validator = Validator::make($input, $this->rules(), $this->fields());

            if($validator->fails()):
                return $this->sendError('Validation Error.', $validator->errors(), 400);   
            endif;

            if (request('income_file')):
                $input['income_file_path'] = $this->saveIncomeFile($request);
            endif;
            
            $income = Income::create($input);

            DB::commit();

            return $this->sendResponse(new IncomeResource($income), 'Income created successfully.', 201);

        } catch (Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage(), [], 422);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */

    public function update(Request $request, $id): JsonResponse
    {
        DB::beginTransaction();

        try {
            $input = $request->all();

            $validator = Validator::make($input, $this->rules(), $this->fields());

            if($validator->fails()):
                return $this->sendError('Validation Error.', $validator->errors(), 400);   
            endif;
            
            $income = Income::find($id);

            if (!$income):
                return $this->sendError('Income not found.');
            endif;

            if (request('income_file')):
                $input['income_file_path'] = $this->saveIncomeFile($request);
            endif;

            $income->update($input);

            DB::commit();

            return $this->sendResponse(New IncomeResource($income), 'Income updated successfully.', 200);

        } catch (Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage(), [], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */

    public function destroy($id): JsonResponse
    {
        DB::beginTransaction();

        try {

            $income = Income::find($id);

            if (!$income):
                return $this->sendError('Income not found.');
            endif;
            
            $income->delete();

            DB::commit();

            return $this->sendResponse([], 'Income deleted successfully');

        } catch (Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage(), [], 422);
        }
    }
    /**
     * Save the Income File in a public folder
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */

    private function saveIncomeFile(Request $request): string {
        return $request->file('income_file')->store('public');
    }

    /**
     * @return Array
     */

    private function rules(): array
    {
        return [
            'customer_id' => ['required', 'numeric'],
            'description' => ['string', 'required', 'max:255'],
            'amount' => ['numeric'],
            'income_date' => ['string'],
            'tax_year' => ['string'],
            'income_file' => ['file', 'mimes:jpg,bmp,png,pdf']
        ];
    }

    /**
     * @return Array
     */

    private function fields(): array
    {
        return [
            'customer_id' => 'Customer ID',
            'description' => 'Description',
            'amount' => 'Amount',
            'income_date' => 'Income Date',
            'tax_year' => 'Tax Year',
            'income_file' => 'Income File'
        ];
    }
}
