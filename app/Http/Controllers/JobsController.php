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
    public function index(){

        $categories = Category::where('status',1)->get();
        $jobTypes = Job_Type::where('status',1)->get();
        $jobs = Job::where('status',1)->with('jobType')->orderby('created_at','DESC')->paginate(9);

        return view('front.jobs',compact('categories','jobTypes','jobs'));
    }
}