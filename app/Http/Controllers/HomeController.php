<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Memo;
use App\Models\Tag;



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
        $memos = Memo::where('user_id',$user['id'])->where('status',1)->orderBy('updated_at','DESC')->get();
        return view('create',compact('user','memos'));
    }


    public function store(Request $request)
    {
        $data = $request->all();
        // dd($data);
        // POSTされたデータをDB（memosテーブル）に挿入
        // MEMOモデルにDBへ保存する命令を出す

        $exist_tag = Tag::where('name',$data['tag'])->where('user_id',$data['user_id'])->first();
        // dd($is_exist);
        if( empty($exist_tag['id']) ){
             //タグをインサート
            $tag_id = Tag::insertGetId(['name' => $data['tag'], 'user_id' => $data['user_id']]);
        }else{
            $tag_id = $exist_tag['id'];
        }

        //タグをインサート
        $tag_id = Tag::insertGetId(['name' => $data['tag'], 'user_id' => $data['user_id']]);
        // dd($tag_id);
        //タグのIDが判明する
        //タグIDをmemosテーブルに入れる

       
        $memo_id = Memo::insertGetId([
            'content' => $data['content'],
             'user_id' => $data['user_id'], 
             'tag_id' => $tag_id,
             'status' => 1
        ]);
        
        // リダイレクト処理
        return redirect()->route('home');
    }

    public function edit($id){
        // 該当するIDのメモをデータベースから取得
        $user = \Auth::user();
        $memo = Memo::where('status', 1)->where('id', $id)->where('user_id', $user['id'])
          ->first();
        //   dd($memo);
        $memos = Memo::where('user_id',$user['id'])->where('status',1)->orderBy('updated_at','DESC')->get();

        $tags = Tag::where('user_id',$user['id'])->get();
        //取得したメモをViewに渡す
        return view('edit',compact('memo','user','memos','tags'));



    }


    public function update(Request $request,$id)
    {
        $inputs = $request->all();
        // dd($inputs);
        Memo::where('id',$id)->update(['content'=> $inputs['content'],'tag_id'=>$inputs['tag_id']]);
        return redirect()->route('home');
    }
    

}
