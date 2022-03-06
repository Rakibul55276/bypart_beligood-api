<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;
use ClientException;

class ForumController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){

        // return view('pages.admin-profile');
    }

    public function forumposts(){

        $forum_category_list=DB::table('forum_category')->where('is_deleted',0)->get()->toArray();
    	return view('pages.forum_management.forum-posts')
               ->with('forum_category_list',$forum_category_list);
    }

    public function getPosts(Request $request){

        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value

        $category=$request->category;

        $category_str="(1=1)";

        if(!empty($category)){
            $category_str="(forum.category=".$category.")";
        }

        $search_str="(1=1)";
        if(!empty($searchValue)){
            $search_str="(forum.subject LIKE '%".$searchValue."%' OR forum.report_text LIKE '%".$searchValue."%' OR first_name LIKE '%".$searchValue."%' OR last_name LIKE '%".$searchValue."%')";
        }
        // Total records
        $totalRecords = DB::table("forum")->select('count(*) as allcount')->count();
        $totalRecordswithFilter = DB::table("forum")->select('count(*) as allcount')->leftjoin('users','forum.user_id','=','users.id')->whereRaw($category_str)->whereRaw($search_str)->count();

        // Fetch records
        $records = DB::table("forum")->orderBy($columnName,$columnSortOrder)
            ->select('forum.*','users.first_name','users.last_name','forum_category.name as category_name')
            ->leftjoin('users','forum.user_id','=','users.id')
            ->leftjoin('forum_category','forum.category','=','forum_category.id')
            ->whereRaw($category_str)
            ->whereRaw($search_str)
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = array();
        $sno = $start+1;
        foreach($records as $record){
            $id = $sno;
            $username = $record->first_name." ".$record->last_name;

            $edit_button_html='<span onclick="editPost(\''.$record->id.'\');"><i class="material-icons" style="color:grey; cursor: pointer; padding:5px;">create</i></span>';

            if($record->is_deleted==1){
                $delete_button_html='<span onclick="restorePost(\''.$record->id.'\');"><i class="material-icons" style="color:green; cursor: pointer; padding:5px;">restore</i></span>';
            }else{
                $delete_button_html='<span onclick="deletePost(\''.$record->id.'\');"><i class="material-icons" style="color:red; cursor: pointer; padding:5px;">delete</i></span>';
            }

            $comment_button_html='<span ><a href="'.config('bypart.admin_url').'/forumcomments?ref='.$record->id.'" target="_blank">View Comments</a></span>';


            $action_html=$edit_button_html." ".$delete_button_html." ".$comment_button_html;

            $report_text=implode(", ",array_unique(explode(", ",$record->report_text)));

            $data_arr[] = array(
                "id" => $id,
                "first_name" => $username,
                "report_text" => $report_text,
                "subject" => $record->subject,
                "name" => $record->category_name,
                "action_html" => $action_html
            );

            $sno++;
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );

        echo json_encode($response);
        exit;
    }

    public function deletePost(Request $request){
        $post_id=$request->id;

        $change_status=DB::table("forum")->where("id",$post_id)->update(["is_deleted"=>1]);
    }

    public function restorePost(Request $request){
        $post_id=$request->id;

        $change_status=DB::table("forum")->where("id",$post_id)->update(["is_deleted"=>0]);
    }

    public function editPost(Request $request){

        $post_id=$request->id;

        $post_details=DB::table("forum")
            ->select('forum.*','users.first_name','users.last_name','forum_category.name as category_name')
            ->leftjoin('users','forum.user_id','=','users.id')
            ->leftjoin('forum_category','forum.category','=','forum_category.id')
            ->where("forum.id",$post_id)
            ->get()->toArray();

        $forum_category_list=DB::table('forum_category')->where('is_deleted',0)->get()->toArray();

        return view('pages.forum_management.edit-post')
               ->with('post_details',$post_details)
               ->with('forum_category_list',$forum_category_list);
    }

    public function modifyPostDetails(Request $request){
        $post_id=$request->id;
        $category=$request->category;
        $subject=$request->subject;

        $update=DB::table("forum")->where("id",$post_id)->update(["category"=>$category,"subject"=>$subject]);
    }

    public function forumComments(){

        $post_id=isset($_GET['ref'])?$_GET['ref']:"";
        $post_id=isset($_POST['ref'])?$_POST['ref']:$post_id;

        $post_details=DB::table("forum")
                    ->select('forum.*','users.first_name','users.last_name','forum_category.name as category_name')
                    ->leftjoin('users','forum.user_id','=','users.id')
                    ->leftjoin('forum_category','forum.category','=','forum_category.id')
                    ->where("forum.id",$post_id)
                    ->get()->toArray();

        return view('pages.forum_management.forum-comments')
               ->with('post_details',$post_details)
               ->with('post_id',$post_id);
    }

    public function getComments(Request $request){

        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value

        $post_id=$request->post_id;


        $search_str="(1=1)";
        if(!empty($searchValue)){
            $search_str="(forum_comment.comment LIKE '%".$searchValue."%' OR forum_comment.report_text LIKE '%".$searchValue."%' OR first_name LIKE '%".$searchValue."%' OR last_name LIKE '%".$searchValue."%')";
        }
        // Total records
        $totalRecords = DB::table("forum_comment")->select('count(*) as allcount')->where("post_id",$post_id)->count();
        $totalRecordswithFilter = DB::table("forum_comment")->select('count(*) as allcount')->leftjoin('users','forum_comment.user_id','=','users.id')->where("post_id",$post_id)->whereRaw($search_str)->count();

        // Fetch records
        $records = DB::table("forum_comment")->orderBy($columnName,$columnSortOrder)
            ->select('forum_comment.*','users.first_name','users.last_name')
            ->leftjoin('users','forum_comment.user_id','=','users.id')
            ->where("post_id",$post_id)
            ->whereRaw($search_str)
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = array();
        $sno = $start+1;
        foreach($records as $record){
            $id = $sno;
            $username = $record->first_name." ".$record->last_name;

            if($record->is_deleted==1){
                $delete_button_html='<span onclick="restoreComment(\''.$record->id.'\');"><i class="material-icons" style="color:green; cursor: pointer; padding:5px;">restore</i></span>';
            }else{
                $delete_button_html='<span onclick="deleteComment(\''.$record->id.'\');"><i class="material-icons" style="color:red; cursor: pointer; padding:5px;">delete</i></span>';
            }


            $action_html=$delete_button_html;

            $report_text=implode(", ",array_unique(explode(", ",$record->report_text)));

            $comment_images=json_decode($record->images);

            $image_str="-";

            if(!empty($comment_images)){

                $image_str="";
                foreach ($comment_images as $key => $value) {
                    $image_str=$image_str.'<a href="'.$value.'" target="_blank">Image '.($key+1).'</a><br>';
                }
            }

            $deleted_text=($record->is_deleted==1)?"Deleted":"";

            $deleted_text='<i style="color:red;">'.$deleted_text.'</i>';

            $data_arr[] = array(
                "id" => $id,
                "first_name" => $username,
                "report_text" => $report_text,
                "comment" => $record->comment,
                "created_at" => $record->created_at,
                "images" => $image_str,
                "is_deleted" => $deleted_text,
                "action_html" => $action_html
            );

            $sno++;
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        );

        echo json_encode($response);
        exit;
    }

    public function deleteComment(Request $request){
        $comment_id=$request->comment_id;

        DB::table("forum_comment")->where("id",$comment_id)->update(["is_deleted"=>1]);
    }

    public function restoreComment(Request $request){
        $comment_id=$request->comment_id;

        DB::table("forum_comment")->where("id",$comment_id)->update(["is_deleted"=>0]);
    }

}
