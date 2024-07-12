<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Models\User;
use App\Models\Category;
use App\Models\Job;
use App\Models\Job_Type;
use App\Models\JobApplication;
use Illuminate\Support\Facades\Validator;
use App\Models\SavedJobs;
use Illuminate\Routing\Route;
use PhpParser\Node\Stmt\Echo_;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class UserController extends Controller
{
    public function index(){
        $users = User::orderBy('created_at','DESC')->paginate(10);
        return view('admin.users.list',compact('users'));
    }
}
