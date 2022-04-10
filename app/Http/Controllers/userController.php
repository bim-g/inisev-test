<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Subscribers;
use App\Models\Websites;

class userController extends Controller
{
    private $user;
    private $error;
    private $subscriber;

    /**
     * userController constructor.
     */
    function __construct()
    {
        $this->user= new User();
        $this->subscriber=new Subscribers();
        $this->error['error']['message']= "";
    }

    /**
     * @return JsonResponse
     */
    function getUsers(): JsonResponse
    {
        return response()->json($this->user->get(),200) ;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    function registerUsers(Request $request): JsonResponse
    {
        $rules=[
            'fname'=>['required','string','max:30'],
            'lname'=>['required','string','max:30'],
            'email'=>['required','email','unique:users','max:60'],
            ];
        $validate=Validator::make($request->all(),$rules);
        if($validate->fails()){
            return response()->json($validate->errors(),400);
        }

        try{
            $this->user->fname=$request->fname;
            $this->user->lname=$request->lname;
            $this->user->email=$request->email;
            $this->user->save();
            return response()->json($this->user,201);

        }catch(\Exception $e){
            $this->error['error']['message']=$e->getMessage();
            return response()->json($this->error,400);
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    function getUsersById(Request $request,$id): JsonResponse
    {
        try{
            $user=$this->user->find($request->id);
            if(!$user){
                $this->error['error']['message']="user Not found!";
                return response()->json($this->error,404);
            }
            return response()->json($user,201);
        }catch(\Exception $ex){
            return response()->json($ex->getMessage(),404);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    function subscribeUser(Request $request): JsonResponse
    {
        $rules=[
            'website_id'=>['required','numeric','min:1'],
            'email'=>['required','email','max:60'],
            ];
        $validate=Validator::make($request->all(),$rules);
        if($validate->fails()){
            return response()->json($validate->errors(),400);
        }
        try{
            $check_website_exist=Websites::get()->find($request->website_id);
            if(!$check_website_exist){
                $this->error['error']['message']="This website does not exist!";
                return response()->json($this->error,400);
            }
            //
            $check_email_exist=$this->subscriber->where('website_id',$request->website_id)->where('email',$request->email)->exists();
            //
            if($check_email_exist){
                $this->error['error']['message']=$request->email." is alreay register to this website";
                return response()->json($this->error,400);
            }
            //
            $this->subscriber->email=$request->email;
            $this->subscriber->website_id=$request->website_id;
            $this->subscriber->save();
            return response()->json($this->subscriber,201);
        }catch(\Exception $e){
            return response()->json($e->getMessage(),400);
        }
    }

    /**
     * @return JsonResponse
     */
    function getSubscribers(): JsonResponse
    {
        try{
            $subscriber=$this->subscriber->get();
            if(!$subscriber){
                $this->error['error']['message']="user Not found!";
                return response()->json($this->error,404);
            }
            return response()->json($subscriber,201);
        }catch(\Exception $ex){
            return response()->json($ex->getMessage(),404);
        }
    }
}
