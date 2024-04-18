<?php

namespace App\Repositories\Interfaces;
use App\Http\Requests\BankDetailsRequest;
use Illuminate\Http\Request;
interface BankDetailsRepositoryInterface{

    public function all();
    public function myBankDetails($user);
    public function getBankDetailsByCurrency($currency, $user_id);
    public function paginator();
    public function findById($id);
    public function create(BankDetailsRequest $request);
    public function update(Request $request, $id);
    public function deleteById($id, $user_id);
}
