<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\MyController;
use App\Models\Profile;
use App\Models\User;
use App\Repositories\Interfaces\WalletRepositoryInterface;
use App\Http\Requests\ProfileRequest;
use App\Repositories\ConcreteClasses\ProfileRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;
use Throwable;


class ProfileController extends MyController
{

    //Constructor to load model using repository design pattern
    protected $profile;
    protected $wallet;
    protected $auth;
    public function __construct(ProfileRepository $repoProfile) {
        $this->auth = auth()->guard('api')->user();
        $this->profile = $repoProfile;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(['status' => 'success', 'data' => $this->profile->myProfile()]);
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
    public function store(ProfileRequest $request)
    {
        try {
            if ($this->auth) {
                $data = [
                    'user_id' => $this->auth->id,
                    'profile_picture' => '',
                ];

                $request->validated();

                if ($request->hasfile('profile_picture')) {
                    $file = $request->file('profile_picture');
                    $name = time() . $file->getClientOriginalName();
                    $filepath = 'profile_picture/' . $name;
                    Storage::disk('public')->put($filepath, file_get_contents($file));

                    // Retrieve the full URL
                    $data['profile_picture'] = Storage::disk('public')->url($filepath);
                }

                if ($request->isJson()) {
                    $request->json()->add($data);
                } else {
                    $request->request->add($data);
                }
                $request->except(['profile_picture']);

                $user = User::findOrFail($this->auth->id);
                $user->is_completed = true;
                $name = $request->first_name . ' ' . $request->last_name;
                $user->name = $name;
                $user->save();

                $profileResponse = $this->profile->createOrUpdate($request, $user->id);
                $data = ["data" => $profileResponse];

                return response()->json([$data, 200, 'Profile successfully updated', 'success']);
            }
        } catch (\Exception $e) {
            Log::error('Profile update error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
        throw new \Exception('Authentication required.');
    }

    private function convertDateFormat($date)
    {
        return \Carbon\Carbon::createFromFormat('d-M-Y', $date)->format('Y-m-d');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

}
