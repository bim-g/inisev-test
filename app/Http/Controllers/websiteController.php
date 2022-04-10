<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
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

    /**
     * @return JsonResponse
     */
    function getWebsites(): JsonResponse
    {
        return response()->json($this->web->get(),200) ;
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    function getWebsiteById($id): JsonResponse
    {
        try{
            $web=$this->web->find($id);
            if(!$web){
                $this->error['error']['message']="website Not found!";
                return response()->json($this->error,404);
            }
            return response()->json($web,201);
        }catch(\Exception $ex){
            return response()->json($ex->getMessage(),404);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    function addwebsite(Request $request): JsonResponse
    {
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
            return response()->json($this->web,201);

        }catch(\Exception $e){
            $this->error['error']['message']=$e->getMessage();
            return response()->json($this->error,400);
        }
    }

    /**
     * @return JsonResponse
     */
    function getWebsiteSubscribers(): JsonResponse
    {
        try {
            $subscribers = $this->web::with('subsribers')->get();
            if (!$subscribers) {
                $this->error['error']['message'] = 'There is no subsribers available';
                return response()->json($this->error, 404);
            }
            return response()->json($subscribers, 201);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), 404);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    function getWebsiteSubscribersByID($id): JsonResponse
    {
        try {
            $subscribers = Websites::with('subsribers')->find($id);
            if (!$subscribers) {
                $this->error['error']['message'] = 'websites not available';
                return response()->json($this->error, 404);
            }
            return response()->json($subscribers, 201);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), 404);
        }
    }/**
     * @return JsonResponse
     */
    function getWebsitePosts(): JsonResponse
    {
        try {
            $subscribers = $this->web::with('posts')->get();
            if (!$subscribers) {
                $this->error['error']['message'] = 'There is no subsribers available';
                return response()->json($this->error, 404);
            }
            return response()->json($subscribers, 201);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), 404);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    function getWebsitePostsByID($id): JsonResponse
    {
        try {
            $subscribers = Websites::with('posts')->find($id);
            if (!$subscribers) {
                $this->error['error']['message'] = 'websites not available';
                return response()->json($this->error, 404);
            }
            return response()->json($subscribers, 201);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), 404);
        }
    }
}
