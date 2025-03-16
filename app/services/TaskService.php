<?php


namespace App\services;


use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskService
{


    //  UPDATE SPECIFIC TASK BY ID
    public function update(Request $request, $id){

        $task = Task::find($id);
        $user = User::where('id', Auth::user()->id)->first();

        if (!is_null($task)) {
            if( $task['user_id'] == $user->id ) {
                $task->update($request->all());
                return response()->json([
                    'task' => $task,
                    'success' => true,
                    'message' => 'task update successfully'
                ],200);
            }
            return response()->json([
                'success' => false,
                'message' => 'You do not have the required authorization.'
            ],403);
        }
        return response()->json([
            'success' => false,
            'message' => 'task not found'
        ],404);
    }

    //  UPDATE SPECIFIC TASK BY ID
    public function delete($id){

        $task = Task::find($id);
        $user = User::where('id', Auth::user()->id)->first();

        if (!is_null($task)) {
            if( $task['user_id'] == $user->id ) {
                $task->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'task deleted successfully'
                ],200);
            }
            return response()->json([
                'success' => false,
                'message' => 'You do not have the required authorization.'
            ],403);
        }
        return response()->json([
            'success' => false,
            'message' => 'task not found'
        ],404);
    }
}
