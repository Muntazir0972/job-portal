<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Models\User;
use App\Models\Category;
use App\Models\Job;
use App\Models\Job_Type;
use Illuminate\Routing\Route;
use PhpParser\Node\Stmt\Echo_;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class JobsController extends Controller
{
    public function index(Request $data){

        $categories = Category::where('status',1)->get();
        $jobTypes = Job_Type::where('status',1)->get();

        $jobs = Job::where('status',1);

        //search using keyword
        if (!empty($data->keyword)) {
            
            $jobs = $jobs->where(function($query) use ($data){
                $query->orWhere('title','like','%'.$data->keyword.'%');
                $query->orWhere('keywords','like','%'.$data->keyword.'%');
                
            });
        }

        //search using location
        if (!empty($data->location)) {
            $jobs = $jobs->where('location',$data->location);
        }

        //search using location
        if (!empty($data->category)) {
                $jobs = $jobs->where('category_id',$data->category);
        }

        $jobTypeArray =[];
        //search using job type
        if (!empty($data->jobType)) {
            //1,2,3
            $jobTypeArray = explode(',',$data->jobType);
            $jobs = $jobs->whereIn('job_type_id',$jobTypeArray);
        }
     
        //search using experience
        if (!empty($data->experience)) {
            $jobs = $jobs->where('experience',$data->experience);
        }


        $jobs = $jobs->with(['jobType','category']);

        if ($data->sort == '0') {

            $jobs = $jobs->orderBy('created_at','ASC');
            
        }else{
            $jobs = $jobs->orderBy('created_at','DESC');
        }

        $jobs = $jobs ->paginate(9);

        return view('front.jobs',compact('categories','jobTypes','jobs','jobTypeArray'));
    }

    public function detail($id){

        $job = Job::where([
                            'id' => $id ,
                            'status' => 1])->with(['jobType','category'])->first();
        if ($job == null) {
            abort(404);
        }
                            

        return view('front.jobDetail',compact('job'));
    }
}