<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use function Symfony\Component\Translation\t;

class TaskController extends Controller
{
    //CREATE NEW TASK
    public function create(TaskRequest $request){
        $user_id = Auth::id();
        $request->merge([
            'user_id'=> $user_id
        ]);
//        return $request->all();
        $task = Task::create($request->all());

        return response()->json([
            'task' => $task,
            'success' => true,
            'message' => 'task create successfully'
        ],201);
    }

//  RETRIEVE A LIST OF TASKS
    public function getTasks(){
        $tasks = Task::all();
        return response()->json([
            'tasks' => $tasks,
            'success' => true,
            'message' => 'all tasks'
        ],200);
    }

//  RETRIEVE SPECIFIC TASK BY ID
    public function getTask($id){
        $task = Task::find($id);
        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'task not found'
            ],404);
        }
            return response()->json([
                'task' => $task,
                'success' => true,
                'message' => 'task'
            ],200);
    }

    //  UPDATE SPECIFIC TASK BY ID
    public function update(TaskUpdateRequest $request, $id){

        $task = Auth::user()->tasks()->find($id);

        if (!$task) {
            throw ValidationException::withMessages(['message' => 'Task not found or unauthorized']);
        }

        $task->update($request->all());

        return response()->json([
            'task' => $task,
            'success' => true,
            'message' => 'task update successfully'
        ],200);

    }

    //  UPDATE SPECIFIC TASK BY ID
    public function delete($id){
        $task = Auth::user()->tasks()->find($id);

        if (!$task) {
            throw ValidationException::withMessages(['message' => 'Task not found or unauthorized']);
        }

        $task->delete();
        return response()->json([
            'success' => true,
            'message' => 'task deleted successfully'
        ],200);
    }
    // Restore deleted tasks
    public function restoreTasks(){
        Task::onlyTrashed()->restore();
        return response()->json([
            'success' => true,
            'message' => 'tasks restore successfully'
        ],200);
    }


    public function searchTasks(Request $request)
    {
        $query = Task::query();

        //Filtering by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        //Filtering by user (user_id)
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $tasks = $query->get();

        return response()->json([
            'tasks' => $tasks,
            'success' => true,
            'message' => 'Tasks retrieved successfully',
        ], 200);
    }
}
