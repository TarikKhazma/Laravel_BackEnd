<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
        public function getAllTasks()
    {
        $tasks=Task::All();
        return response()->json($tasks, 200);
    }
        public function addCategoriesToTask(Request $request, int $taskId)
        {
                $request->validate([
                        'category_id' => 'required|array',
                        'category_id.*' => 'integer|exists:categories,id',
                ]);

                $user_id=Auth::user()->id;
                $task=Task::findOrFail($taskId);
                if ($task->user_id != $user_id)
                    return response()->json(['message'=>'Unauthorized'], 403);
                $task->categories()->syncWithoutDetaching($request->category_id);
                return response()->json('Category attached sucessfully', 200);
        }

    public function addToFavorites($taskId)
    {
        $user_id=Auth::user()->id;
        $task=Task::findOrFail($taskId);
        if ($task->user_id != $user_id)
            return response()->json(['message'=>'Unauthorized'], 403);
        /** @var \App\Models\User $user */
        $user=Auth::user();
        $user->favoriteTasks()->syncWithoutDetaching($taskId);
        return response()->json(['message'=>'Task added to favorites'], 200);
    }

    public function removeFromFavorites($taskId)
    {
        $user_id=Auth::user()->id;
        $task=Task::findOrFail($taskId);
        if ($task->user_id != $user_id)
            return response()->json(['message'=>'Unauthorized'], 403);
        /** @var \App\Models\User $user */
        $user=Auth::user();
        $user->favoriteTasks()->detach($taskId);
        return response()->json(['message'=>'Task removed from favorites'], 200);
    }

    public function getFavoriteTasks()
    {
        /** @var \App\Models\User $user */
        $user=Auth::user();
        $tasks=$user->favoriteTasks;
        return response()->json($tasks, 200);
    }

     public function getTasksByPirority($order = 'desc')
    {
        // desc (الافتراضي): high ← medium ← low
        // asc            : low ← medium ← high
        if ($order == 'asc')
            $priorities="'low', 'medium', 'high'";
        else
            $priorities="'high', 'medium', 'low'";

        /** @var \App\Models\User $user */
        $user=Auth::user();
        $tasks=$user->tasks()->orderByRaw("FIELD(priority, $priorities)")->get();
        return response()->json($tasks, 200);
    }

    public function index()
    {
        $tasks=Auth::user()->tasks;
        return response()->json($tasks, 200);
    }

    public function store(StoreTaskRequest $request)
    {
        $user_id=Auth::user()->id;
        $validatedData=$request->validated();
        $validatedData['user_id']=$user_id;
        $task=Task::create($validatedData);
        return response()->json($task, 201);
    }

    public function update(UpdateTaskRequest $request, int $taskId)
    {
        $user_id=Auth::user()->id;
        $task=Task::findOrFail($taskId);
        if ($task->user_id != $user_id)
            return response()->json(['message'=>'Unauthorized'], 403);
        $task->update($request->validated());
        return response()->json($task, 200);
    }

    public function show(int $taskId)
    {
        $user_id=Auth::user()->id;
        $task=Task::findOrFail($taskId);
        if ($task->user_id != $user_id)
            return response()->json(['message'=>'Unauthorized'], 403);
        return response()->json($task, 200);
    }

    public function destroy(int $taskId)
    {
        $user_id=Auth::user()->id;
        $task=Task::findOrFail($taskId);
        if ($task->user_id != $user_id)
            return response()->json(['message'=>'Unauthorized'], 403);
        $task->delete();
        return response()->json(null, 204);
    }

    public function getTaskUser(int $taskId)
    {
        $user_id=Auth::user()->id;
        $task=Task::findOrFail($taskId);
        if ($task->user_id != $user_id)
            return response()->json(['message'=>'Unauthorized'], 403);
        $user=$task->user;
        return response()->json($user, 200);
    }
}
