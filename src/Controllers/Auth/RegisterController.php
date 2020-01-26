<?php

namespace App\Http\Controllers\StlcAuth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Validator;
use Sagartakle\Laracrud\Models\Role;
use App\User;

class RegisterController extends Controller
{
    protected $data = []; // the information we send to the view

    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */
    use RegistersUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');

        // Where to redirect users after login / registration.
        $this->redirectTo = property_exists($this, 'redirectTo') ? $this->redirectTo
            : config('stlc.route_prefix', 'dashboard');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $user_model_fqn = config('stlc.user_model_fqn');
        $user = new $user_model_fqn();
        $users_table = $user->getTable();

        return Validator::make($data, [
            'first_name'     => 'required|max:255',
            'last_name'     => 'required|max:255',
            'email'    => 'required|email|max:255|unique:'.$users_table,
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     *
     * @return User
     */
    protected function create(array $data)
    {
        $user = [];
        $user = \DB::transaction(function() use ($data) {
            try{
                $user_model_fqn = config('stlc.user_model_fqn');
                $user = new $user_model_fqn();
                $user->name = $data['first_name']." ".$data['last_name'];
                $user->email = $data['email'];
                $user->password = bcrypt($data['password']);
                $user->save();
                $user->roles()->attach(Role::where('name', 'Super_admin')->first()->id);
                return $user;
            } catch (\Exception $e) {
                \DB::rollback();
                if(isset($data->src_ajax)) {
                    return response()->json(['status' => 'validation_error', 'massage' => 'Validation Error', 'errors' => $e->getMessage()]);
                } else {
                    return redirect()->back()->withErrors($e->getMessage())->withInput();
                }
            }
        });
        
        return $user;
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        // if registration is closed, deny access
        if (!config('stlc.registration_open')) {
            abort(403, trans('base.registration_closed'));
        // } elseif(User::all()->count() > "0") {
        //     return redirect()->guest(config('stlc.route_prefix', 'admin').'/login');
        }

        // $this->data['title'] = trans('base.register'); // set the page title

        return view('stlcauth.register', $this->data);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        // if registration is closed, deny access
        if (!config('stlc.registration_open')) {
            abort(403, trans('base.registration_closed'));
        }

        $this->validator($request->all())->validate();

        $this->guard()->login($this->create($request->all()));

        return redirect($this->redirectPath());
    }
}