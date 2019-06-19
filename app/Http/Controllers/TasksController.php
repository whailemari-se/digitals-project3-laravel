<?php

namespace App\Http\Controllers;

use App\Task;
use App\Project;
use App\TaskUser;
use App\User;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

class TasksController extends Controller
{

     public function index()
     {
         //
         if( Auth::check() ){
             $tasks = Task::where('user_id', Auth::user()->id)->get();

              return view('tasks.index', ['tasks'=> $tasks]);
         }
         return view('auth.login');
     }


     public function adduser(Request $request){
         //add user to tasks

         //take a task, add a user to it
         $task = Task::find($request->input('task_id'));



         if(Auth::user()->id == $task->user_id){

         $user = User::where('email', $request->input('email'))->first(); //single record

         //check if user is already added to the task
         $taskUser = taskUser::where('user_id',$user->id)
                                    ->where('task_id',$task->id)
                                    ->first();

            if($taskUser){
                //if user already exists, exit

                return response()->json(['success' ,  $request->input('email').' is already a member of this task']);

            }


            if($user && $task){

                $task->users()->attach($user->id);

                     return response()->json(['success' ,  $request->input('email').' was added to the task successfully']);

                    }

         }

         return redirect()->route('tasks.show', ['task'=> $task->id])
         ->with('errors' ,  'Error adding user to task');



     }




     public function create( $task_id = null )
     {
         //

         $tasks = null;
         if(!$task_id){
            $tasks = Project::where('user_id', Auth::user()->id)->get();
         }

         return view('tasks.create',['task_id'=>$task_id, 'projects'=>$tasks]);
     }


     public function store(Request $request)
     {



        $task = Task::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'project_id' => $request->input('project_id'),
            'user_id' => Auth::user()->id
        ]);


        if($task){
            return redirect()->route('projects.show', ['task'=> $task->id])
            ->with('success' , 'task created successfully');
        }



        return back()->withInput()->with('errors', 'Error creating new task');

     }




     public function show(Task $task)
     {



        $task = Task::find($task->id);

        $comments = $task->comments;
         return view('tasks.show', ['task'=>$task, 'comments'=> $comments ]);
     }


     public function edit(Task $task)
     {

         $task = Task::find($task->id);

         return view('tasks.edit', ['task'=>$task]);
     }


     public function update(Request $request, task $task)
     {

     

       $taskUpdate = Task::where('id', $task->id)
                                 ->update([
                                         'name'=> $request->input('name'),
                                         'description'=> $request->input('description')
                                 ]);

       if($taskUpdate){
           return redirect()->route('tasks.show', ['task'=> $task->id])
           ->with('success' , 'task updated successfully');
       }
       //redirect
       return back()->withInput();



     }


     public function destroy(Task $task)
     {
         //

         $findtask = Task::find( $task->id);
         if($findtask->delete()){

             //redirect
             return redirect()->route('tasks.index')
             ->with('success' , 'task deleted successfully');
         }

         return back()->withInput()->with('error' , 'task could not be deleted');


     }
}
