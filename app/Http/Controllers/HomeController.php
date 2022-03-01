<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Memo;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        //ログインしているユーザー情報をviewに渡す
        $user =\Auth::user();
        //メモ一覧を取得 DESC降順 ASC昇順
        $memos = Memo::where('user_id',$user['id'])->where('status',1)->orderBy('updated_at','DESC')->get();
        // dd($memos);
        return view('home',compact('user','memos'));
    }
    

    public function create()
    {
        //ログインしているユーザー情報をviewに渡す
        $user =\Auth::user();
        return view('create',compact('user'));
    }


    public function store(Request $request)
    {
        $data = $request->all();
        // dd($data);
        // POSTされたデータをDB（memosテーブル）に挿入
        // MEMOモデルにDBへ保存する命令を出す

       
        $memo_id = Memo::insertGetId([
            'content' => $data['content'],
             'user_id' => $data['user_id'], 
             'status' => 1
        ]);
        
        // リダイレクト処理
        return redirect()->route('home');
    }
    

}
