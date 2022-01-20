<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController as BaseController;
use App\Http\Resources\Customer as CustomerResource;
use App\Http\Resources\CustomerListReport;
use App\Models\Customer;
use Illuminate\Http\Request;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CustomerController extends BaseController
{

    /**
     * Display a listing of the resource.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $customers = Customer::all();

            return $this->sendResponse(CustomerResource::collection($customers), 'Customer retrieved successfully.');

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
            $customer = Customer::find($id);

            if (!$customer):
                return $this->sendError('Customer not found.'); 
            endif;

            return $this->sendResponse(new CustomerResource($customer), 'Customer retrieved successfully.');

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
        
            $validator = Validator::make($input,$this->rules(), $this->fields());

            if ($validator->fails()):
                return $this->sendError('Validation Error.', $validator->errors(), 400);   
            endif;

            if (request('profile_pic')):
                $input['profile_pic_path'] = $this->customerProfilePic($request);
            endif;
            
            $customer = Customer::create($input);

            DB::commit();

            return $this->sendResponse(new CustomerResource($customer), 'Customer created successfully.', 201);

        } catch(Exception $e) {
            
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
            $customer = Customer::find($id);

            if (!$customer):
                return $this->sendError('Customer not found.'); 
            endif;

            $validator = Validator::make($input, $this->rules($customer->id), $this->fields());

            if($validator->fails()):
                return $this->sendError('Validation Error.', $validator->errors(), 400);   
            endif;

            if (request('profile_pic')):
                $input['profile_pic_path'] = $this->customerProfilePic($request);
            endif;
            
            $customer->update($input);

            DB::commit();

            return $this->sendResponse(new CustomerResource($customer), 'Customer updated successfully.', 200);

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

            $customer = Customer::find($id);

            if (!$customer):
                return $this->sendError('Customer not found.'); 
            endif;

            $customer->delete();

            DB::commit();
            
            return $this->sendResponse([], 'Customer deleted successfully.');

        } catch (Exception $e) {
            
            DB::rollBack();

            return $this->sendError($e->getMessage(), [], 422);
        }
    }

    /**
     * Display a list of the Customers with incomes
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     * 
     */
    public function reportListing(Request $request): JsonResponse
    {
        try {

            $input = $request->all();

            $validator = Validator::make($input, $this->rulesForReportList(), $this->fields());

            if($validator->fails()):
                return $this->sendError('Validation Error.', $validator->errors(), 400);   
            endif;

            $startDate = $input['start_date'] ?? null;
            $endDate = $input['end_date'] ?? null;


            $list = Customer::customersWithIncomes($startDate, $endDate);

            return $this->sendResponse(new CustomerListReport($list), 'Report retrieved successfully');

        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), [], 422);
        }
    }

    /**
     * Save the Profile Picture in a public folder
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    private function customerProfilePic(Request $request): string
    {
        return $request->file('profile_pic')->store('public');
    }
    
    /**
     * @param int $id
     * @return Array
     */
    private function rules($id = 0): array
    {
        return [
            'name' => ['required', 'string'],
            'email' => ['required','email','unique:customers,email,'.$id],
            'utr' => ['required','numeric', 'unique:customers,utr,'.$id],
            'dob' => ['date'],
            'phone' => ['string'],
            'profile_pic' => ['file', 'mimes:jpg,bmp,png'],
            
        ];
    }

    /**
     * @return Array
     */
    private function rulesForReportList(): array
    {
        $todayDate = date('Y-m-d');
         
        return [
            "start_date" => [
                "date",
                "date_format:Y-m-d",
                "before:endDate",
                "before_or_equal:".$todayDate
            ], 
            "end_date" => [
                "date",
                "date_format:Y-m-d",
                "after_or_equal:startDate",
            ]
        ];
    }

    /**
     * @return Array
     */
    private function fields(): array
    {
        return [
            "start_date" => "Start Date",
            "end_date" => "End Date",
            "name" => "Name",
            "email" => "Email",
            "utr" => "UTR",
            "dob" => "DOB",
            "phone" => "Phone",
            "profile_pic" => "Profile Picture"
        ];
    }

}
