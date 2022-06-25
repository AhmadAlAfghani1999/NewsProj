<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\News;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }
    public function getNews(Request $request)
    {

        $news=News::orderby('id','DESC')->
        when($request->title,function ($n) use ($request){
           $n->where('title','like','%'.$request->title.'%');
        })->when($request->date_time,function ($n) use ($request){
            $n->where('date_time','like','%'.$request->date_time.'%');
        })->when($request->category_id,function ($n) use ($request){
            $n->where('category_id','like','%'.$request->category_id.'%');
        })->
        with('category')->get();
        return response()->json($news);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function newcategory(Request $request)
    {
        //new category
        $validated = Validator::make($request->all(), [
            'name' => 'required|unique:categories',

        ],[],[
            'name'=>'اسم التصنيف',

        ]);
        if ($validated->fails()){
            $msg="تأكد من البيانات المدخلة";
            $data=$validated->errors();
            return response()->json(compact('msg','data'),404);
        }
        $category=new Category();
        $category->name=$request->name;
        $category->save();
        return response()->json(["msg"=>"تمت الاضافة بنجاح"]);
    }


    public function addnews(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'title' => 'required|max:50',
            'details' => 'required',
            'date_time' => 'required',
            'image' => 'mimes:jpeg,jpg,png,gif|sometimes|max:10000',
            'category_id' => 'required',
        ],[],[
            'title'=>'العنوان',
            'details'=>'التفاصيل',
            'date_time'=>'تاريخ الخبر',
            'image'=>'الصورة',
            'category_id'=>'التصنيف',

        ]);
        if ($validated->fails()){
            $msg="تأكد من البيانات المدخلة";
            $data=$validated->errors();
            return response()->json(compact('msg','data'),404);
        }
        $path=$request->file('image')->store('public/storage/image');
        $news = new News();
        $news->title=$request->title;
        $news->details=$request->details;
        $news->date_time=$request->date_time;
        $news->image=$path;
        $news->category_id=$request->category_id;
        $news->save();
        return response()->json(["msg"=>"تمت الاضافة بنجاح"]);

    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    public function getNewsId($id)
    {
        $news=News::Find($id)->with('category')->where('id',$id)->get();;
        return response()->json(compact('news'));
    }
    public function getCategoriesId($id)
    {
        $categories=Category::Find($id);
        return response()->json(compact('categories'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function updatecategory(Request $request, $id)
    {
        $validated = Validator::make($request->all(), [
            'name' => 'required|unique:categories,name,'.$id,

        ],[],[
            'name'=>'اسم التصنيف',


        ]);
        if ($validated->fails()){
            $msg="تأكد من البيانات المدخلة";
            $data=$validated->errors();
            return response()->json(compact('msg','data'),404);
        }
        $category= Category::Find($id);
        $category->name=$request->name;
        $category->save();
        return response()->json(["msg"=>"تم التعديل بنجاح"]);
    }
    public function updatenews(Request $request, $id)
    {
        $validated = Validator::make($request->all(), [
            'title' => 'required|max:50',
            'details' => 'required',
            'date_time' => 'required',
            'image' => 'mimes:jpeg,jpg,png,gif|sometimes|max:10000',
            'category_id' => 'required',
        ],[],[
            'title'=>'العنوان',
            'details'=>'التفاصيل',
            'date_time'=>'تاريخ الخبر',
            'image'=>'الصورة',
            'category_id'=>'التصنيف',

        ]);
        if ($validated->fails()){
            $msg="تأكد من البيانات المدخلة";
            $data=$validated->errors();
            return response()->json(compact('msg','data'),404);
        }
        $path=$request->file('image')->store('public/image');
        $news = News::Find($id);
        $news->title=$request->title;
        $news->details=$request->details;
        $news->date_time=$request->date_time;
        $news->image=$path;
        $news->category_id=$request->category_id;
        $news->save();
        return response()->json(['msg'=>"تم التعديل بنجاح"]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $deletenews = News::find($id);
        if ($deletenews){
            $deletenews->delete();
            return response()->json(['msg'=>"تم الحذف"]);
        }else {
            return response()->json(['msg'=>"تأكد من رقم الخبر الذي تريد حذفه"]);

        }

    }


    public function login(Request $request){
//        dd('sssss');
        $user=User::where('email',$request->email)->first();
        if (!$user){
            return response()->json(['msg'=>"عذرا هذا الايميل غير صحيح"],401);
        }

        if (Hash::check($request->password,$user->password)){
            $token=$user->createToken('laravel password Grant client')->accessToken;
            $response=['token'=>$token];
            return response($response,200);
        }else{
            $response=["msg"=>"عذرا كلمة المرور خطأ"];
            return response($response,422);
        }
    }

}
