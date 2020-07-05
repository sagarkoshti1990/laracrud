<?php

namespace Sagartakle\Laracrud\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

use App\User;

class LoginController extends Controller
{
    protected $data = []; // the information we send to the view

    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
    use AuthenticatesUsers {
        logout as defaultLogout;
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
        // ----------------------------------
        // If not logged in redirect here.
        $this->loginPath = config('stlc.route_prefix', 'admin').'/login';
        // Redirect here after successful login.
        $this->redirectTo = config('stlc.route_prefix', 'admin').'/dashboard';
        // Redirect here after logout.
        $this->redirectAfterLogout = config('stlc.route_prefix', 'admin');
        // ----------------------------------
    }

    // -------------------------------------------------------
    // Laravel overwrites for loading lara views
    // -------------------------------------------------------

    /**
     * Show the application login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        $this->data['title'] = 'Login'; // set the page title

        if(User::all()->count()) {
            return view(config('stlc.stlc_modules_folder_name','stlc::').'auth.login', $this->data);
        } else {
            return redirect()->guest(config('stlc.route_prefix', 'admin').'/register');
        }
    }

    /**
     * Log the user out and redirect him to specific location.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        // Do the default logout procedure
        $this->defaultLogout($request);

        // And redirect to custom location
        return redirect($this->redirectAfterLogout);
    }
}
