<?php

namespace App\Http\Controllers;

use App\Mail\NotifyMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Models\Email;
use App\Models\Posts;
use App\Models\Subscribers;
use App\Models\User;
use App\Models\Websites;

class postController extends Controller
{
    private $post;
    private $error;

    function __construct()
    {
        $this->post=new Posts();
        $this->error['error']['message']="";

    }

    /**
     * @return JsonResponse
     */
    function getPosts(): JsonResponse
    {
        $posts=$this->post->get();
        return response()->json($posts,200) ;
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    function getPostById($id): JsonResponse
    {
        try{
            $post=$this->post::with(['postBy','website','emails'])->find($id);
            if(!$post){
                $this->error['error']['message']="Posts Not found!";
                return response()->json($this->error,404);
            }
            return response()->json($post,201);
        }catch(\Exception $ex){
            return response()->json($ex->getMessage(),404);
        }
    }

    /**
     * Delete Existing Post
     * @param $id
     * @return JsonResponse
     */
    function deletePost($id): JsonResponse
    {
        try{
            $result=$this->post->where('id',$id)->delete($id);
            if(!$result){
                $this->error['error']['message']="Posts has been Deleted!";
                return response()->json($this->error,404);
            }
            return response()->json(['success'=>['message'=>'Posts has been Deleted with success!']],201);
        }catch(\Exception $ex){
            return response()->json($ex->getMessage(),404);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    function AddPost(Request $request): JsonResponse
    {
        try{
            $rules=[
                'title'=>['required','string','unique:posts','min:3','max:100'],
                'description'=>['required','string','min:10','max:200'],
                'content'=>['required','string','min:10'],
                'post_by'=>['required','numeric','min:1'],
                'website_id'=>['required','numeric','min:1'],
                ];
            $validate=Validator::make($request->all(),$rules);
            if($validate->fails()){
                return response()->json($validate->errors(),400);
            }
            //
            $check_website_exist=Websites::get()->find($request->website_id);
            if(!$check_website_exist){
                $this->error['error']['message']="This website does not exist is alreay register to this website";
                return response()->json($this->error,400);
            }
            //
            $check_user_exist=User::get()->find($request->post_by);
            if(!$check_user_exist){
                $this->error['error']['message']="User does not exist";
                return response()->json($this->error,400);
            }
            //
            $this->post->title=$request->title;
            $this->post->description=$request->description;
            $this->post->body=$request->content;
            $this->post->website_id=$request->website_id;
            $this->post->post_by=$request->post_by;
            $this->post->save();
            //
            $data=[
                "title"=>$request->title,
                "description"=>$request->description,
                "post_id"=>$this->post->id,
                "website_id"=>$check_website_exist->id,
            ];

            $save_email=$this->saveEmail($data);
            if(isset($save_email['exception'])){
                throw new \Exception($save_email['exception']);
            }
            return response()->json($this->post,201);
        }catch(\Exception $e){
            $this->error['error']['message']=$e->getMessage();
            return response()->json($this->error,400);
        }
    }
    /**
     * @param array $data
     * @return array|bool|void
     */
    private function saveEmail(array $data){
        try{
            $subscribers = Subscribers::get()->where('website_id',$data['website_id']);
            if(!$subscribers){
                return ;
            }
            $members=[];
            foreach($subscribers as $subscriber){
                $members[]=[
                    'post_id'=>$data['post_id'],
                    'email'=>$subscriber->email,
                    'subject'=>$data['title'],
                    'text_body'=>$data['description'],
                    'status'=>false
                ];
            }
            Email::insert($members);
            return $this->sendEmails();
        }catch(\Exception $e){
            return ['exception'=>$e->getMessage()];
        }
    }

    /**
     * @return array|bool|void
     */
    private function sendEmails(){
        try{
            $mailQues=Email::get()->where('status',false);
            if($mailQues->isEmpty()){
                return ;
            }
            foreach($mailQues as $mail){
                Mail::to($mail->email)
                    ->send(new NotifyMail($mail));
                if(Mail::failures()){
                    throw new \Exception("Email not send");
                }
                $mail->status=true;
                $mail->save();
            }
            return true;
        }catch(\Exception $e){
            return ['exception'=>$e->getMessage()];
        }
    }

    /**
     * @return JsonResponse
     */
    function getPostsEmail(): JsonResponse{
        try {
            $postsEmails=$this->post::with('emails')->get();
            if(count($postsEmails)<1){
                $this->error['error']['message'] = 'There is no posts available';
                return response()->json($this->error, 404);
            }
            return response()->json($postsEmails,200);
        }catch (\Exception $ex){
            $this->error['error']['message']=$ex->getMessage();
            return response()->json($this->error,400);
        }
    }
    /**
     * @param $status
     * @return JsonResponse
     */
    function getPostEmailByStatus($status): JsonResponse
    {
        try {
            $postsEmails=Email::with('post')->get()->where('status',$status);
            if(count($postsEmails)<1){
                $this->error['error']['message'] = 'There is no email with such status';
                return response()->json($this->error, 404);
            }
            return response()->json($postsEmails,200);
        }catch (\Exception $ex){
            $this->error['error']['message']=$ex->getMessage();
            return response()->json($this->error,400);
        }
    }
}
