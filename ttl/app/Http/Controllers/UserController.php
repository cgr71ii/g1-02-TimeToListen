<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Publication;
use Cookie;
use Redirect;

class UserController extends Controller
{

    public function login(Request $request)
    {
        if  (session('user') !== null)
        {
            {
                // We update pagination recalling the methods again.

                $publications = Publication::where('user_id', session('user')->id)->simplePaginate(1);

                session(['publications' => $publications]);
            }

            return view('user.profile');
            //return view('user.profile', ['user' => session('user')]);
        }

        if ($request->has('username') && $request->has('password'))
        {
            if ($request->has('remember'))
            {
                // Creating cookies.

                // 15 days.
                $minutes = 60 * 24 * 15;

                Cookie::queue(Cookie::make('TTLusername', $request->username, $minutes));
                Cookie::queue(Cookie::make('TTLpassword', $request->password, $minutes));
            }

            // Checking if user is valid.

            $count = User::where('email', $request->username)->count();

            if ($count == 1)
            {
                $user = User::where('email', $request->username)->first();

                if ($user->password === $request->password)
                {
                    //$publications = $user->publication->paginate(1);
                    $publications = Publication::where('user_id', $user->id)->simplePaginate(1);

                    session([   'user' => $user,
                                'publications' => $publications]);

                    return view('user.profile');
                }
            }

            return redirect('/')->with('loginfail', true);
        }

        return redirect('/');
    }

    public function logout()
    {
        session(['user' => null]);

        return redirect('/');
    }

    public function signup(Request $request)
    {
        if ($request->has('name') && $request->has('lname') && 
            $request->has('username') && $request->has('password') && 
            $request->has('birthday'))
        {
            if (empty($request->name) || empty($request->lname) || 
                empty($request->username) || empty($request->password) ||
                empty($request->birthday))
            {
                return redirect('/')->with('signupfailemptyfield', true);
            }

            $count = User::where('email', $request->username)->count();

            if ($count != 0)
            {
                return redirect('/')->with('signupfailuserexists', true);
            }

            $user = new User([  'email' => $request->username,
                                'password' => $request->password,
                                'name' => $request->name,
                                'lastname' => $request->lname,
                                'birthday' => "$request->birthday 00:00:00",
                                'pic_profile_path' => 'default-user.png']);
            $user->save();

            session(['user' => $user]);

            return redirect('/profile');
            //return redirect('/profile')->with('user', $user);
        }

        return redirect('/')->with('signupfail', true);
    }

}