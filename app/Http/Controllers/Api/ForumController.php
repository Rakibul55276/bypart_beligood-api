<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Forum;
use App\Models\ForumComment;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\Notification\NotificationController;

use DateTime;
use DateTimeZone;


class ForumController extends ApiController
{
    public function __construct() {

        $date = new DateTime("now", new DateTimeZone('Asia/Kuala_Lumpur'));
        $this->currentdate = $date->format('Y-m-d H:i:s');

    }

    public function getPosts(Request $request){

    	$page=isset($request->page)?$request->page:1;
    	$limit=isset($request->limit)?$request->limit:10;

        $category_id=isset($request->category_id)?$request->category_id:'all';

        $user_id=Auth::user()->id;

        $category_sql_str="(1=1)";

        if($category_id!='all'){
            $category_sql_str="(category=".$category_id.")";
        }

    	$offset=($page-1)*$limit;

    	$posts=Forum::selectRaw("forum.*, users.first_name,users.last_name,users.avatar,forum_category.name,(SELECT COUNT(id) FROM forum_comment WHERE forum_comment.post_id=forum.id) as total_comments")
    				  ->leftjoin("users","users.id","=","forum.user_id")
    			      ->leftjoin("forum_category","forum_category.id","=","forum.category")
                      ->where("forum.is_deleted",0)
    			      ->whereRaw($category_sql_str)
    			      ->orderBy("forum.created_at","desc")
    			      ->paginate($limit);

        foreach ($posts as $key => $value) {

            $posts[$key]['liked_by_user']=null;

            $liked_by_user=DB::table("forum_like_transaction")->where("user_id",$user_id)->where("transaction_id",$value['id'])->count();

            if($liked_by_user>0){
                $posts[$key]['liked_by_user']=1;
            }

            $posts[$key]['reported_by_user']=null;
            $reported_by_user=DB::table("forum_report_transaction")->where("user_id",$user_id)->where("transaction_type","post")->where("transaction_id",$value['id'])->count();

            if($reported_by_user>0){
                $posts[$key]['reported_by_user']=1;
            }
        }


        return $this->successResponse($posts);
    }

    public function topTrendingTopic(Request $request){

        $user_id=Auth::user()->id;
        $category_id=isset($request->category_id)?$request->category_id:0;

        $posts=Forum::selectRaw("forum.*, users.first_name,users.last_name,users.avatar,forum_category.name,(SELECT COUNT(id) FROM forum_comment WHERE forum_comment.post_id=forum.id) as total_comments")
                      ->leftjoin("users","users.id","=","forum.user_id")
                      ->leftjoin("forum_category","forum_category.id","=","forum.category")
                      ->where("forum.is_deleted",0)
                      ->where("forum.category",$category_id)
                      ->orderBy("forum.like_count","desc")
                      ->orderBy("forum.created_at","desc")
                      ->limit(1)
                      ->get()->toArray();

        foreach ($posts as $key => $value) {

            $posts[$key]['liked_by_user']=null;

            $liked_by_user=DB::table("forum_like_transaction")->where("user_id",$user_id)->where("transaction_id",$value['id'])->count();

            if($liked_by_user>0){
                $posts[$key]['liked_by_user']=1;
            }

            $posts[$key]['reported_by_user']=null;
            $reported_by_user=DB::table("forum_report_transaction")->where("user_id",$user_id)->where("transaction_type","post")->where("transaction_id",$value['id'])->count();

            if($reported_by_user>0){
                $posts[$key]['reported_by_user']=1;
            }
        }

        return $this->successResponse($posts);

    }

    public function getIndividualPost(Request $request){

    	$post_id=isset($request->post_id)?$request->post_id:0;

        $user_id=Auth::user()->id;

    	$post=Forum::selectRaw("forum.*, users.first_name,users.last_name,users.avatar,forum_category.name,(SELECT COUNT(id) FROM forum_comment WHERE forum_comment.post_id=forum.id) as total_comments")
    				  ->leftjoin("users","users.id","=","forum.user_id")
    			      ->leftjoin("forum_category","forum_category.id","=","forum.category")
    			      ->where("forum.id",$post_id)
    			      ->get()->toArray();

        foreach ($post as $key => $value) {
            $post[$key]['liked_by_user']=null;
            $liked_by_user=DB::table("forum_like_transaction")->where("user_id",$user_id)->where("transaction_id",$value['id'])->count();

            if($liked_by_user>0){
                $post[$key]['liked_by_user']=1;
            }

            $post[$key]['reported_by_user']=null;
            $reported_by_user=DB::table("forum_report_transaction")->where("user_id",$user_id)->where("transaction_type","post")->where("transaction_id",$value['id'])->count();

            if($reported_by_user>0){
                $post[$key]['reported_by_user']=1;
            }
        }

        return $this->successResponse($post);

    }

    public function getComments(Request $request){

    	$post_id=isset($request->post_id)?$request->post_id:1;
    	$page=isset($request->page)?$request->page:1;
    	$limit=isset($request->limit)?$request->limit:30;

        $user_id=Auth::user()->id;

        $comment_only_sql="(forum_comment.parent_comment_id IS NULL)";
    	$comments=ForumComment::selectRaw("forum_comment.*, users.first_name,users.last_name,users.avatar")
    				  ->leftjoin("users","users.id","=","forum_comment.user_id")
                      ->where("forum_comment.post_id",$post_id)
    			      ->whereRaw($comment_only_sql)
    			      ->orderBy("forum_comment.created_at","desc")
    			      ->paginate($limit);

        foreach ($comments as $key => $value) {
            $comments[$key]['liked_by_user']=null;
            $liked_by_user=DB::table("comment_like_transaction")->where("user_id",$user_id)->where("transaction_id",$value['id'])->count();

            if($liked_by_user>0){
                $comments[$key]['liked_by_user']=1;
            }

            $comments[$key]['reported_by_user']=null;
            $reported_by_user=DB::table("forum_report_transaction")->where("user_id",$user_id)->where("transaction_type","comment")->where("transaction_id",$value['id'])->count();

            if($reported_by_user>0){
                $comments[$key]['reported_by_user']=1;
            }

            $replies=ForumComment::selectRaw("forum_comment.*, users.first_name,users.last_name,users.avatar")
                      ->leftjoin("users","users.id","=","forum_comment.user_id")
                      ->where("forum_comment.parent_comment_id",$value->id)
                      ->orderBy("forum_comment.created_at","asc")
                      ->get()->toArray();

            foreach ($replies as $k => $v) {
                $replies[$k]['liked_by_user']=null;
                $liked_by_user=DB::table("comment_like_transaction")->where("user_id",$user_id)->where("transaction_id",$v['id'])->count();

                if($liked_by_user>0){
                    $replies[$k]['liked_by_user']=1;
                }

                $replies[$k]['reported_by_user']=null;
                $reported_by_user=DB::table("forum_report_transaction")->where("user_id",$user_id)->where("transaction_type","comment")->where("transaction_id",$v['id'])->count();

                if($reported_by_user>0){
                    $replies[$k]['reported_by_user']=1;
                }
            }

            $comments[$key]['replies']=$replies;
        }


        return $this->successResponse($comments);

    }

    public function getReplies(Request $request){

        $comment_id=isset($request->comment_id)?$request->comment_id:1;

        $user_id=Auth::user()->id;

        $comments=ForumComment::selectRaw("forum_comment.*, users.first_name,users.last_name,users.avatar")
                      ->leftjoin("users","users.id","=","forum_comment.user_id")
                      ->where("forum_comment.parent_comment_id",$comment_id)
                      ->orderBy("forum_comment.created_at","asc")
                      ->get()->toArray();


        return $this->successResponse($comments);

    }

    public function likeTransaction(Request $request){

    	$user_id=Auth::user()->id;

    	$like_type=$request->type;
    	$transaction_id=$request->transaction_id;

    	if($like_type=='forum'){
    		$table_name='forum_like_transaction';
    		$parent_table='forum';
    	}else{
    		$table_name='comment_like_transaction';
    		$parent_table='forum_comment';
    	}

    	$check_like_existance=DB::table($table_name)->where("user_id",$user_id)->where("transaction_id",$transaction_id)->count();

    	if($check_like_existance>0){
    		$delete_transaction=DB::table($table_name)->where("user_id",$user_id)->where("transaction_id",$transaction_id)->delete();
    		$decrement_like_count=DB::table($parent_table)->where("id",$transaction_id)->decrement("like_count");
    	}else{
    		$insertArray=array();
    		$insertArray['user_id']=$user_id;
    		$insertArray['transaction_id']=$transaction_id;

    		$insert_transaction=DB::table($table_name)->where("user_id",$user_id)->where("transaction_id",$transaction_id)->insert($insertArray);
    		$increment_like_count=DB::table($parent_table)->where("id",$transaction_id)->increment("like_count");
    	}


    	return $this->successResponse(array(),"Like Updated Successfully");

    }

    public function getCategoryList(){
    	$categories=DB::table("forum_category")->get()->toArray();
    }

    public function addPost(Request $request){
    	$user_id=Auth::user()->id;

    	$insertArray=array();

    	$insertArray['user_id']=$user_id;
    	$insertArray['category']=isset($request->category)?$request->category:0;
    	$insertArray['subject']=isset($request->subject)?$request->subject:"";
    	$insertArray['post']=isset($request->post)?$request->post:"";
    	$insertArray['cover_image']=$request->cover_image;
        $insertArray['created_at']=$this->currentdate;

    	if($insertArray['category']==0){

    		return $this->errorResponse("Whoops! No Category Selected.", 400);

    	}else if(empty($insertArray['subject'])&&empty($insertArray['post'])){

    		return $this->errorResponse("Whoops! Post & Subject Empty.", 400);

    	}else{
    		$insert=DB::table("forum")->insert($insertArray);

    		return $this->successResponse(array(),"Topic Added Successfully");
    	}

    }

    public function addComment(Request $request){
    	$user_id=Auth::user()->id;

    	$insertArray=array();

    	$insertArray['user_id']=$user_id;
        $insertArray['comment']=isset($request->comment)?$request->comment:"";
    	$insertArray['images']=isset($request->images)?$request->images:"";
    	$insertArray['post_id']=isset($request->post_id)?$request->post_id:0;
        $insertArray['created_at']=$this->currentdate;

    	if($insertArray['post_id']==0){

    		return $this->errorResponse("Whoops! No Associated Post Found.", 400);

    	}else if(empty($insertArray['comment'])&&empty($insertArray['images'])){

    		return $this->errorResponse("Whoops! Comment & Image Empty.", 400);

    	}else{
    		$insert=DB::table("forum_comment")->insert($insertArray);

    		return $this->successResponse(array(),"Comment Added Successfully");
    	}

    }

    public function addReply(Request $request){

    	$user_id=Auth::user()->id;

    	$insertArray=array();

    	$insertArray['user_id']=$user_id;
    	$insertArray['comment']=isset($request->comment)?$request->comment:"";
        $insertArray['images']=isset($request->images)?$request->images:"";
    	$insertArray['post_id']=isset($request->post_id)?$request->post_id:0;
    	$insertArray['reply_type']=isset($request->reply_type)?$request->reply_type:"reply";
    	$insertArray['parent_comment_id']=isset($request->comment_id)?$request->comment_id:0;
        $insertArray['created_at']=$this->currentdate;

    	if($insertArray['post_id']==0){

    		return $this->errorResponse("Whoops! No Associated Post Found.", 400);

    	}else if($insertArray['parent_comment_id']==0){

    		return $this->errorResponse("Whoops! No Associated Parent Comment Found.", 400);

    	}else if(empty($insertArray['comment'])&&empty($insertArray['images'])){

    		return $this->errorResponse("Whoops! Reply Comment & Image Empty.", 400);

    	}else{
    		$insert=DB::table("forum_comment")->insert($insertArray);

    		return $this->successResponse(array(),"Comment Added Successfully");
    	}

    }

    public function deletePost(Request $request){

    	$user_id=Auth::user()->id;

    	$post_id=isset($request->post_id)?$request->post_id:0;

    	$post_exist_check=DB::table("forum")->where("id",$post_id)->where("user_id",$user_id)->count();

    	if($post_id==0){

    		return $this->errorResponse("Whoops! No Associated Post Found.", 400);

    	}else if($post_exist_check==0){
    		return $this->errorResponse("Whoops! You are not Authorized to delete this post.", 400);
    	}else{
    		$delete=DB::table("forum")->where("id",$post_id)->where("user_id",$user_id)->update(["is_deleted"=>1]);

    		return $this->successResponse(array(),"Topic Deleted Successfully");
    	}


    }

    public function deleteComment(Request $request){

    	$user_id=Auth::user()->id;

    	$comment_id=isset($request->comment_id)?$request->comment_id:0;

    	$comment_exist_check=DB::table("forum_comment")->where("id",$comment_id)->where("user_id",$user_id)->count();

    	if($comment_id==0){

    		return $this->errorResponse("Whoops! No Associated Comment Found.", 400);

    	}else if($comment_exist_check==0){
    		return $this->errorResponse("Whoops! You are not Authorized to delete this comment.", 400);
    	}else{
    		$delete=DB::table("forum_comment")->where("id",$comment_id)->where("user_id",$user_id)->update(["is_deleted"=>1]);

    		return $this->successResponse(array(),"Comment Deleted Successfully");
    	}


    }

    public function reportPost(Request $request){

        $post_id=isset($request->post_id)?$request->post_id:0;
    	$comment_id=isset($request->comment_id)?$request->comment_id:0;

        $report_text=isset($request->report_text)?$request->report_text:"";

        $report_text_new=$report_text;
        $report_type="";
        $ref_id="";

        $user_id=Auth::user()->id;

        $insertArray=array();
        $insertArray['user_id']=$user_id;
        $insertArray['report_text']=$report_text;

        if(!empty($report_text)){

            if(!empty($post_id)){
                DB::table("forum")->where("id",$post_id)->increment("report_count");

                $existing_report_text=DB::table("forum")->where("id",$post_id)->value("report_text");

                if(!empty($existing_report_text)){
                    $report_text=$existing_report_text.", ".$report_text;
                }

                DB::table("forum")->where("id",$post_id)->update(["report_text"=>$report_text]);


                $insertArray['transaction_id']=$post_id;
                $insertArray['transaction_type']='post';

                $report_type="forum_post";
                $ref_id=$post_id;
            }

            if(!empty($comment_id)){
                DB::table("forum_comment")->where("id",$comment_id)->increment("report_count");

                $existing_report_text=DB::table("forum_comment")->where("id",$comment_id)->value("report_text");

                if(!empty($existing_report_text)){
                    $report_text=$existing_report_text.", ".$report_text;
                }

                DB::table("forum_comment")->where("id",$comment_id)->update(["report_text"=>$report_text]);

                $insertArray['transaction_id']=$comment_id;
                $insertArray['transaction_type']='comment';

                $report_type="forum_comment";
                $ref_id=$comment_id;
            }

            DB::table("forum_report_transaction")->insert($insertArray);

            /**
             * Call Notification Global Saving API Start
             */

            $user = Auth::user();
            $notification_header = 'User Report';
            $notification_content = $user->first_name . " " . $user->last_name . " Reported ".$report_text_new." on Forum. Please click to View Details.";
            $notification_type = $report_type;
            $ref_id = $ref_id;
            $sender_id = $user->id;
            $user_ids = DB::table('users')->select("id")->where("user_type","admin")->get()->toArray();
            $user_ids = array_column(json_decode(json_encode($user_ids)), "id");

            if($report_type=='forum_post'){
                $url = config('bypart.admin_url') . '/forumposts';
            }else{
                $url = config('bypart.admin_url') . '/forumcomments?ref='.$ref_id;
            }


            app(NotificationController::class)->saveNotification($notification_header, $notification_content, $notification_type, $url, $sender_id, $user_ids, $request->listing_id);
            /**
             * Call Notification Global Saving API End
             */

            return $this->successResponse(array(),"Your Report has been sent Successfully.");

        }else{
            return $this->errorResponse("No Report Type Selected.", 400);
        }

    }
}
