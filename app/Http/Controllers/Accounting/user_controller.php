<?php

namespace App\Http\Controllers\Accounting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use DB;

class user_controller extends Controller
{
    protected $register_validation_rules = [
        'phone' => 'string|required|unique:users,phone',
        'first_name' => 'required|string',
        'last_name' => 'required|string',
        'email' => 'required|email|unique:users,email',
        'gender' => 'required|boolean',
        'password' => 'required|min:8'
    ];

    public function register(Request $request)
    {
       $this->validate($request,$this->register_validation_rules);

       $user_record = [];
       foreach($this->register_validation_rules as $key => $value)
       {
           if($request->filled($key))
           {
               if($key == 'password'){
                    $user_record[$key] = sha1($request->$key);

                    continue ;
               }

               $user_record[$key] = $request->$key ;
           }
       }

       $now = Carbon::now();
       $user_record['last_login_date'] = $now;
       $user_record['created_at'] = $now;
       $user_record['updated_at'] = $now;

       DB::table('users')->insert($user_record);

       return response()->json([
           'status' => true,
           'msg' => 'user registerd successfully.'
       ],201);
    }

    public function do_login(Request $request)
    {
        $this->validate($request,[
            'email' => 'string|required',
            'password' => 'string|required',
        ]);

        $email = strip_tags($request->email);
        $password = sha1($request->password);

        $user_record = DB::table('users')->where([
            ['email','=',$email],
            ['password','=',$password]
        ])->get()
        ->first();

        if($user_record)
        {
            $this->set_session_info($user_record);

            return response()->json([
                'status' => true,
                'msg' => 'user logged in successfully'
            ]);
        }
        else{
            return response()->json([
                'status' => false,
                'msg' => 'login failed!'
            ]);
        }

    }

    protected function set_session_info($user)
    {
        session([
            'user_id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
        ]);
    }

    public function get_users_list()
    {
        $users = DB::table('users')->get();

        return response()->json([
            'status' => true,
            'users' => $users
        ]);
    }

    public function logout()
    {
        \Session::flush();
        \Session::save();

    }


}
