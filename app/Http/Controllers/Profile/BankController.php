<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\MyController;
use Illuminate\Http\Request;
use App\Http\Requests\BankDetailsRequest;
use App\Repositories\Interfaces\BankDetailsRepositoryInterface;
use App\Helpers\PaystackService;
use Illuminate\Support\Facades\Log;
use App\Models\BankDetail;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class BankController extends MyController
{

    //Constructor to load model using repository design pattern
    protected $bank_details;
    protected $paystackService;
    protected $auth;
    public function __construct(BankDetailsRepositoryInterface $bankDetailsInterface, PaystackService $paystackService)
    {
        $this->bank_details = $bankDetailsInterface;
        $this->auth = auth()->guard('api')->user();
        $this->paystackService = $paystackService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // dd($this->auth);
        return response()->json(
            [
                'status' => 'success',
                'data' => $this->bank_details->myBankDetails($this->auth)
            ],
            201
        );

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BankDetailsRequest $request)
    {
        try {
            // Validate the request
            $validatedData = $request->validated();

            $allBankNames = $this->getAllBankNames();
            $bankName = $validatedData['bank_name'];

            $selectedBank = collect($allBankNames['data'])->firstWhere('name', $bankName);

            if (!$selectedBank) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bank not found.',
                ], 404);
            }

            $bankDetails = new BankDetail();
            $bankDetails->fill($validatedData);
            $bankDetails->bank_code = $selectedBank['code'];
            $bankDetails->user_id = $this->auth->id;
            $bankDetails->save();

            return response()->json([
                'status' => 'success',
                'data' => $bankDetails,
            ], 201);
        } catch (ValidationException $ve) {
            return response()->json(['message' => $ve->getMessage(), 'status' => 'error'], 422);
        } catch (ModelNotFoundException $mfe) {
            return response()->json(['message' => $mfe->getMessage(), 'status' => 'error'], 404);
        } catch (\Exception $e) {
            Log::error('Error in bankController: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while processing your request. ' . $e->getMessage(), 'status' => 'error'], 500);
        }
    }


    public function getBankDetailsByCurrency(Request $request)
    {
        $currency = $request->query('currency');

        $data = $this->bank_details->getBankDetailsByCurrency($currency, $this->auth->id);
        return response()->json(
            [
                'status' => 'success',
                'data' => $data
            ],
            200
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = $this->bank_details->findById($id);
        return response()->json(['data' => $data != null ? $data : 'Not Found', 'status' => 'success']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $status = $this->bank_details->update($request, $id);
        return response()->json(['message' => 'successfully updated', 'status' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->bank_details->deleteById($id, $this->auth->id);
    }

    public function verifyAccount(Request $request)
    {
        try {
            $request->validate([
                'bank_code' => 'required|string',
                'account_number' => 'required|string'
            ]);

            $bankCode = $request->input('bank_code');
            $accountNumber = $request->input('account_number');

            $verificationResult = $this->paystackService->bankAccountVerification($bankCode, $accountNumber);

            return response()->json($verificationResult);
        } catch (\Exception $e) {
            Log::error('Error in bankController: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while processing your request', 'status' => 'error'], 500);
        }
    }

    public function getAllBankNames(): array
    {
        return $this->paystackService->fetchAllBanks();
    }

}
