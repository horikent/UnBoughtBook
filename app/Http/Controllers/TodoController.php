<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TodoController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $todos = Todo::sortable()->whereHas('user', function ($query) {
            $query->where('user_id',  Auth::user() -> id);
    })->get();
        $param = [
            'todos' =>$todos,
            // 'date' => $date,
            // 'book' => $book,
            // 'author' => $author,
            // 'publisher' => $publisher,
            'user' =>$user,
        ];
            return view('index', $param);
    }

    public function store(Request $request){
        $todo = new Todo();
        $todo -> fill($request -> all());
        $todo -> user_id = Auth::user()->id;
        $todo -> save();
        return redirect('/index') -> route('index',$todo);
    }    
    
    public function construct()
    {
    $this->middleware('auth');
}

    public function find()
    {
        $tags = Tag::all();
        $user = Auth::user();
        $param = [
        'user' =>$user,
        'tags' => $tags,
        'input' => ''
    ];
        return view('find', $param);
    }

    public function search(Request $request)
    {
    $tags = Tag::all();
    $user = Auth::user();
    $user_id = Auth::id();
    $keyword = $request->task;
    $tag_id = $request->tag_id;
    if (!empty($keyword)) {
        $search = Todo::where('task', 'like binary', "%{$keyword}%")->get();
    }
    if (!empty($tag_id)) {
        $search = Todo::where('tag_id', 'like binary', "%{$tag_id}%")->get();
    }         
    $result = $search->where('user_id', '=', $user_id);
        $param = [
            'tag_id' => $tag_id,
            'search' => $result,
            'tags' => $tags,
            'user' => $user
        ];
            return view('find', $param);       
    }

    public function create(Request $request)
    {
        $user_id = Auth::user()->id;
        $date = $request->date;
        $book = $request->book;
        $author = $request->author;
        $publisher = $request->publisher;
        $param = [
            'date' => $date,
            'book' => $book,
            'author' => $author,
            'publisher' => $publisher,
            'user_id' => $user_id
        ];            
        Todo::create($param);
            if (!empty($user_id)) {
            $todo = Todo::where('user_id',  "$user_id")->get();
        }
        return redirect('/index');
	}

    public function update(Request $request)
    {
        $user_id = Auth::user()->id;
        $param = [
            'task' => $request->task,
            'tag_id'=> $request->tag_id,
            'user_id' => $user_id,
            '_token' => $request->_token
        ];    
        unset($param['_token']);        
        Todo::where('id', $request->id)->update($param);
        return redirect('/index ');
	}    

    public function remove(Request $request)
    {
        Todo::find($request->id)->delete();
        return redirect('/index');
    }      
    
}    