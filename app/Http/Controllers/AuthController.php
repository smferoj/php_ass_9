<?php


namespace App\Http\Controllers;
use App\Models\Exam;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PasswordReset;
use App\Models\Subject;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    // user register
    public function loadRegister()
    {
        return view('register');
    }
    public function studentRegister(Request $request)
    {
        $request->validate([
            'name' => 'string|required|min:3',
            'email' => 'string|email|required|max:70|unique:users',
            'mobile' => 'string|required|max:12|unique:users',
            'mobile' => 'string|required|regex:/^[0-9]{10,12}$/',
            'gurdian_mobile' => 'string|required|regex:/^[0-9]{10,12}$/',
            'gurdian_mobile' => 'string|required|max:12|unique:users',
            'password' => 'string|required|confirmed|min:8'
        ]);

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->mobile = $request->mobile;
        $user->gurdian_mobile = $request->gurdian_mobile;
        $user->password = Hash::make($request->password);
        $user->save();
        return redirect('/login')->with('success', 'You are registerered and can now log in now');
    }


 // login
 public function loadLogin(){
    if(Auth::user() && Auth::user()->is_admin ==1){
        return redirect('/admin/dashboard');
    }else if(Auth::user() && Auth::user()->is_admin ==0){
        return redirect('/dashboard');
    }
    return view('/login');
 }

 public function userLogin(Request $request){
    // dd($request);
    $request->validate([
        'email'=>'string|required|email',
        'password'=>'string|required',
    ]);


   $userCredential = $request->only('email','password');
    if(Auth::attempt($userCredential)){
         if(Auth::user()->is_admin ==1){
            return redirect('/admin/dashboard');
         }else{
            return redirect('/dashboard');
         }
    }else{
        return back()->with('error','Username & Password mismatched');
    }

 }

 // User-role 
 public function loadDashboard(){
    return view('student.dashboard');
 }

 public function adminDashboard(){
    $subjects = Subject::all();
    return view('admin.dashboard', compact('subjects'));
 }

 public function logout(Request $request){
    $request->session()->flush();
    Auth::logout();
    return redirect('/');
}

// forget passsword
public function forgetPasswordLoad()
    {
        return view('forget-password');
    }


    public function forgetPassword(Request $request)
    {
        try {
            $user = User::where('email', $request->email)->get();
            if (count($user) > 0) {
                $token = Str::random(40);
                $domain = URL::to('/');
                $url = $domain.'/reset-password?token='.$token;
                $data['url']= $url;
                $data['email'] = $request->email;
                $data['title'] = 'Password Reset';
                $data['body'] = 'Please click ob below link to reset your password';

                Mail::send('forgetPasswordMail', ['data'=>$data], function($message) use($data){
                    $message->to($data['email'])->subject($data['title']);
                });

               $dateTime =  Carbon::now()->format('Y-m-d H:i:s');
                PasswordReset::updateOrCreate(
                    ['email'=>$request->email],
                    [
                        'email'=>$request->email,
                        'token'=>$token,
                        'created_at'=>$dateTime
                    ]
                );
                return back()->with('success','Please Check your mail to reset your password! ');

            } else {
                return back()->with('error','Email is not exist!');
            }
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function resetPasswordLoad(Request $request){
           $resetData = PasswordReset::where('token', $request->token)->get();

           if(isset($request->token) && count($resetData)> 0){
             $user = User::where('email', $resetData[0]['email'])->get();
             return view('resetPassword', compact('user'));
           }else{
            return view('404');
           }
    }
    public function resetPassword(Request $request){
        $request->validate([
            'password'=>'required|string|min:6|confirmed'
        ]);

        $user = User::find($request->id);
        $user->password = Hash::make($request->password);
        $user->save();

        PasswordReset::where('email', $user->email)->delete();

        return redirect('/login')->with('success', 'Password changed successfully. You can now log in.');

    }



}