<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Websites;

class websiteController extends Controller
{
    private $web;
    private $error;
    function __construct()
    {
        $this->web= new Websites();
        $this->error['error']['message']="";
    }
    function getWebsites(){
        return response()->json($this->web->get(),200) ;
    }
    function getWebsiteById(Request $request,$id){
        try{
            $web=$this->web->find($request->id)->posts;
            if(!$web){
                $this->error['error']['message']="website Not found!";
                return response()->json($this->error,404);               
            }
            return response()->json($web,201);
        }catch(\Exception $ex){
            return response()->json($ex->getMessage(),404);
        }
    }
    function addwebsite(Request $request){
        $rules=[
            'name'=>['required','string','unique:websites','min:3'],
            'url'=>['required','URL','unique:websites']
            ];
        $validate=Validator::make($request->all(),$rules);
        if($validate->fails()){
            return response()->json($validate->errors(),400);
        }
        
        try{
            $this->web->name=$request->name;
            $this->web->url=$request->url;
            $this->web->created_by=1;
            $this->web->save();
            return response()->json(['Website Add with Success!'],201);

        }catch(\Exception $e){
            $this->error['error']['message']=$e->getMessage();
            return response()->json($this->error,400);
        }  
    }
}
