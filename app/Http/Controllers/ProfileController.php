<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProfileRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $profiles = Profile::all();
        return response()->json($profiles, 200);
    }

    public function store(StoreProfileRequest $request)
    {
        $userId = Auth::user()->id;

        // كل مستخدم إله بروفايل واحد بس
        if (Profile::where('user_id', $userId)->exists())
            return response()->json(['message' => 'هاد المستخدم عنده بروفايل من قبل'], 422);

        $validated = $request->validated();
        $validated['user_id'] = $userId;
        $path = $request->file('image')->store('my photo', 'public');
        $validated['image'] = $path;
        $profile = Profile::create($validated);
        return response()->json([
            'message' => 'Profile created successfully.',
            'profile' => $profile,
        ], 201);
    }

    public function show(int $id)
    {
        $profile = Profile::where('user_id',$id)->firstOrfail();
        return response()->json($profile, 200);
    }

    public function update(UpdateProfileRequest $request, int $id)
    {
        $profile = Profile::findOrFail($id);
        $profile->update($request->validated());
        return response()->json([
            'message' => 'تم تعديل البروفايل بنجاح',
            'data' => $profile,
        ], 200);
    }

    public function destroy(int $id)
    {
        $profile = Profile::findOrFail($id);
        $profile->delete();
        return response()->json([
            'message' => 'تم حذف البروفايل بنجاح',
        ], 200);
    }
}
