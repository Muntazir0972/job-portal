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

class AccountController extends Controller
{
    public function registration(){
        return view('front.account.registration');
    }

    public function processRegistration(Request $data){
        
        $validator = Validator::make($data->all(),[
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:5|same:confirm_password',
            'confirm_password' => 'required',
        ]);

        if ($validator->passes()) {
            
            $user = new User();
            $user->name = $data->name;
            $user->email = $data->email;
            $user->password = Hash::make($data->password);
            $user->save();

            session()->flash('success','You have registered successfully.');

            return response()->json([
                'status' => true,
                'errors' => []
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function login(){
        return view('front.account.login');
    }

    public function authenticate(Request $data){

        $validator = Validator::make($data->all(),[
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->passes()) {

            if (Auth::attempt(['email' => $data->email, 'password' => $data->password])) {
                
                return redirect()->route('account.profile');

            } else {
                return redirect()->route('account.login')->with('error','Either Email/Password is incorrect');
            }


        } else {
            return redirect()->route('account.login')
            ->withErrors($validator)
            ->withInput($data->only('email'));
        }

    }

    public function profile(){

        $id = Auth::user()->id;
        $user = User::where('id',$id)->first();
        return view('front.account.profile',compact('user'));
    }

    public function updateProfile(Request $data){

        $id = Auth::user()->id;

        $validator = Validator::make($data->all(),[
            'name' => 'required|min:5|max:20',
            'email' => 'required|email|unique:users,email,'.$id.',id'
        ]);

        if ($validator->passes()) {
            
            $user = User::find($id);
            $user->name = $data->name;
            $user->email = $data->email;
            $user->mobile = $data->mobile;
            $user->designation = $data->designation;
            $user->save();

            session()->flash('success','Profile Updated Succesfully');

            return response()->json([
                'status' => true,
                'errors' => []
            ]);

        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }   

    public function logout(){
        Auth::logout();
        return redirect()->route('account.login');
    }

    public function updateProfilePic(Request $data){

        $id = Auth::user()->id;

        $validator = Validator::make($data->all(),[
            'image' => 'required|image',
        ]);

        if ($validator->passes()) {
            
            $image = $data->file('image');
            $ext = $image ->getClientOriginalExtension();
            $imageName = $id.'-'.time().'.'.$ext;   
            $image->move(public_path('/profile_pic/'),$imageName);



            //To create a small thumbnail
            $sourcePath = public_path('/profile_pic/'.$imageName);
            $manager = new ImageManager(Driver::class);
            $image = $manager->read($sourcePath);
            
            $image->cover(150,150); 
            $image->toPng()->save(public_path('/profile_pic/thumb/'.$imageName));

            //Delete old profile pic
            File::delete(public_path('/profile_pic/thumb/'.Auth::user()->image));
            File::delete(public_path('/profile_pic/'.Auth::user()->image));


        User::where('id',$id)->update(['image' => $imageName]);

        session()->flash('success','Profile Picture Updated Successfully');

        return response()->json([
            'status' => true,
            'errors' => []
        ]);

        } else {

            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function createJob(){

        $categories = Category::orderBy('name','ASC')->where('status',1)->get();

        $jobTypes = Job_Type::orderBy('name','ASC')->where('status',1)->get();

        return view('front.account.job.create',compact('categories','jobTypes'));
    }

    public function saveJob(Request $data){

        $rules =[
            'title' => 'required|min:5|max:200',
            'category' => 'required',
            'jobType' => 'required',
            'vacancy' => 'required|integer',
            'location' => 'required|max:50',
            'description' => 'required|min:3|max:75',
            'company_name' => 'required',
        ];

        $validator = Validator::make($data->all(),$rules);

        if ($validator->passes()) {
            
            $job = new Job();
            $job->title = $data->title;
            $job->category_id = $data->category;
            $job->job_type_id = $data->jobType;
            $job->vacancy = $data->vacancy;
            $job->salary = $data->salary;
            $job->location = $data->location;
            $job->description = $data->description;
            $job->benefits = $data->benefits;
            $job->responsibility = $data->responsibility;
            $job->qualifications = $data->qualifications;
            $job->keywords = $data->keywords;
            $job->experience = $data->experience;
            $job->company_name = $data->company_name;
            $job->company_location = $data->company_location;
            $job->company_website = $data->company_website;
            $job->save();

            session()->flash('success','Job Added Succesfully.');

            return response()->json([
                'status' => true,
                'errors' => []
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function myJob(){
        return view('front.account.job.my-jobs');
    }


}
