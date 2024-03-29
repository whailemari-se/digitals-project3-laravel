<?php

namespace App\Http\Controllers;

use App\Project;
use App\Company;
use App\ProjectUser;
use App\User;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

class ProjectsController extends Controller
{

     public function index()
     {

         if( Auth::check() ){
             $projects = Project::where('user_id', Auth::user()->id)->get();

              return view('projects.index', ['projects'=> $projects]);
         }
         return view('auth.login');
     }


     public function adduser(Request $request){
         //add user to projects

         //take a project, add a user to it
         $project = Project::find($request->input('project_id'));

        //construct another middleware?

         if(Auth::user()->id == $project->user_id){

         $user = User::where('email', $request->input('email'))->first(); //single record

         //check if user is already added to the project
         $projectUser = ProjectUser::where('user_id',$user->id)
                                    ->where('project_id',$project->id)
                                    ->first();

            if($projectUser){
                //if user already exists, exit

                return response()->json(['success' ,  $request->input('email').' is already a member of this project']);

            }


            if($user && $project){

                $project->users()->attach($user->id);

                     return response()->json(['success' ,  $request->input('email').' was added to the project successfully']);

                    }

         }

         return redirect()->route('projects.show', ['project'=> $project->id])
         ->with('errors' ,  'Error adding user to project');



     }




     public function create( $company_id = null )
     {
         //

         $companies = null;
         if(!$company_id){
            $companies = Company::where('user_id', Auth::user()->id)->get();
         }

         return view('projects.create',['company_id'=>$company_id, 'companies'=>$companies]);
     }


     public function store(Request $request)
     {


        $project = Project::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'company_id' => $request->input('company_id'),
            'user_id' => Auth::user()->id
        ]);


        if($project){
            return redirect()->route('projects.show', ['project'=> $project->id])
            ->with('success' , 'project created successfully');
        }



        return back()->withInput()->with('errors', 'Error creating new project');

     }




     public function show(Project $project)
     {

        // $project = Project::where('id', $project->id )->first();
        $project = Project::find($project->id);

        $comments = $project->comments;
         return view('projects.show', ['project'=>$project, 'comments'=> $comments ]);
     }

     public function edit(Project $project)
     {
         //
         $project = Project::find($project->id);

         return view('projects.edit', ['project'=>$project]);
     }


     public function update(Request $request, project $project)
     {

       //save data

       $projectUpdate = Project::where('id', $project->id)
                                 ->update([
                                         'name'=> $request->input('name'),
                                         'description'=> $request->input('description')
                                 ]);

       if($projectUpdate){
           return redirect()->route('projects.show', ['project'=> $project->id])
           ->with('success' , 'project updated successfully');
       }
       //redirect
       return back()->withInput();



     }


     public function destroy(Project $project)
     {
         //

         $findproject = Project::find( $project->id);
         if($findproject->delete()){

             //redirect
             return redirect()->route('projects.index')
             ->with('success' , 'project deleted successfully');
         }

         return back()->withInput()->with('error' , 'project could not be deleted');


     }
}
