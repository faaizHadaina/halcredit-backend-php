<?php

namespace App\Repositories\ConcreteClasses;
use App\Repositories\Interfaces\BankDetailsRepositoryInterface;
use App\Models\BankDetail;
use App\Http\Requests\BankDetailsRequest;
use Illuminate\Http\Request;
class BankDetailsRepository implements BankDetailsRepositoryInterface{


    public function all(){
        $data = BankDetail::all()
            ->map->format();
        return $data;
    }

    public function myBankDetails($user){
        $data = BankDetail::where('user_id', $user->id)->get()
            ->map->format();
        return $data;
    }

    public function getBankDetailsByCurrency($currency, $user_id) {
        $transactions = BankDetail::where('user_id', $user_id)
                                           ->where('currency', $currency)
                                           ->get();
        $formattedTransactions = $transactions->map->format();
        return $formattedTransactions;
    }

    public function paginator(){
        return BankDetail::paginate(10)
            ->getCollection()
            ->map->format();
    }

    public function findById($id){
        return BankDetail::where('id', $id)->first();
    }

    public function update(Request $request, $id){
        return BankDetail::where('id', $id)->update($request);
    }

    public function create(BankDetailsRequest $request){
        if($request->isJson()){
            $request->json()->add(['user_id' => auth()->guard('api')->user()->id]);
        }else{
            $request->request->add(['user_id' => auth()->guard('api')->user()->id]);
        }
        return BankDetail::create($request->all());
    }

    public function deleteById($id, $user_id)
{
    $deletedRows = BankDetail::where('id', $id)
                             ->where('user_id', $user_id)
                             ->delete();

    if ($deletedRows > 0) {
        return response()->json(['message' => 'Bank detail deleted successfully.'], 200);
    } else {
        return response()->json(['message' => 'No bank detail found or already deleted.'], 404);
    }
}

}
