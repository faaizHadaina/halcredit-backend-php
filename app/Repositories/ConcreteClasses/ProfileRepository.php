<?php

namespace App\Repositories\ConcreteClasses;

use App\Http\Requests\ProfileRequest;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ProfileRepository
{
    protected $user;

    public function __construct()
    {
        $this->user = auth()->guard('api')->user();
    }

    protected function saveFile($file, $type)
    {
        try {
            if ($file && $file->isValid()) {
                $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/bmp', 'image/svg+xml'];
                $contentType = $file->getMimeType();

                if (!in_array($contentType, $allowedMimeTypes)) {
                    throw new \Exception('The file must be a valid image.');
                }

                $pathPrefix = 'profile_pictures/';

                $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
                $filepath = $pathPrefix . $filename;
                Storage::disk('local')->put('public/' . $filepath, file_get_contents($file));

                return $filepath;
            }
        } catch (FileException $e) {
            Log::error('File could not be saved: ' . $e->getMessage());
            throw new \Exception('There was an error uploading the image.');
        } catch (\Exception $e) {
            Log::error('An error occurred: ' . $e->getMessage());
            throw $e;
        }
    }


    public function myProfile()
    {
        return $this->user->profile->format();
    }

    public function UpdateProfile(Request $request)
    {
        return $this->user->profile->update($request);
    }

    public function createOrUpdate(ProfileRequest $request, $id)
    {
        try {
            if (!$request instanceof ProfileRequest) {
                Log::error('Request is not an instance of ProfileRequest. Actual type: ' . gettype($request));
                abort(500, 'Server Error: Incorrect request type');
            }

            $user = User::findOrFail($id);
            $profile = Profile::where('user_id', $id)->first();

            $profileData = $request->validated();
            $profileData['user_id'] = $id;

            if ($request->has('first_name') || $request->has('last_name')) {
                $firstName = $request->input('first_name', '');
                $lastName = $request->input('last_name', '');

                $currentNames = explode(' ', $user->name, 2);
                $currentFirstName = $currentNames[0] ?? '';
                $currentLastName = $currentNames[1] ?? '';

                $firstName = empty($firstName) ? $currentFirstName : $firstName;
                $lastName = empty($lastName) ? $currentLastName : $lastName;

                $user->name = trim($firstName . ' ' . $lastName);
                $user->save();
            }

            // Adjust phone number
            if ($request->has('phone') && Str::startsWith($request->phone, '+234')) {
                $profileData['phone'] = '0' . substr($request->phone, 4);
            }

            if ($request->hasFile('profile_picture') && $request->file('profile_picture')->isValid()) {
                $profilePicPath = $this->saveFile($request->file('profile_picture'), 'profile_picture');
                $profileData['profile_picture'] = $profilePicPath;
            }

            if ($profile) {
                $profile->update($profileData);
            } else {
                $profile = Profile::create($profileData);
            }

            return $profile->format();
        }catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }



    public function update(Request $request, $id)
    {
        $profile = Profile::where('user_id', $id)->first();
        if (!is_null($profile)) {
            $profile->update($request->all());
        } else {
            $profile = Profile::create($request->all());
        }

        return $profile->format();
    }
}
