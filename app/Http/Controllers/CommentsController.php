<?php

namespace App\Http\Controllers;

use App\Comment;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

class CommentsController extends Controller
{



    public function store(Request $request)
    {

        $comment = Comment::create([
            'body' => $request->input('body'),
            'url' => $request->input('url'),
            'commentable_type' => $request->input('commentable_type'),
            'commentable_id' => $request->input('commentable_id'),
            'user_id' => Auth::user()->id
        ]);


        if($comment){
            return back()->with('success' , 'Comment created successfully');
        }



        return back()->withInput()->with('errors', 'Error creating new comment');

    }

}
