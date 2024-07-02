<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Routing\Route;
use PhpParser\Node\Stmt\Echo_;

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


}
