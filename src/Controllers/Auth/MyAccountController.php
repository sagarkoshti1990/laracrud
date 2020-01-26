<?php

namespace App\Http\Controllers\StlcAuth;

use Alert;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Http\Requests\AccountInfoRequest;
use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Support\Facades\Hash;

use App\Models\Module;
use App\User;

class MyAccountController extends Controller
{
    protected $data = [];

    /**
     * Show the user a form to change his personal information.
     */
    public function getAccountInfoForm()
    {
        $this->data['title'] = trans('base.my_account');
        $this->data['user'] = $this->guard()->user();
        $this->data['crud'] = Module::make('Employees');
        $this->data['crud']->datatable = true;
        if(isset(Auth::user()->id) && isset(Auth::user()->context()->id)) {
            $this->data['crud']->row = Auth::user()->context();
        }
        
        return view('auth.account.update_info', $this->data);
    }

    /**
     * Save the modified personal information for a user.
     */
    public function postAccountInfoForm(AccountInfoRequest $request)
    {
        // $user_request = ['name' => $request->first_name.' '.$request->last_name,'email' => $request->email];
        // $result = $this->guard()->user()->update($user_request);
        // $context = ['primary_email' => $request->email, 'first_name' => $request->first_name, 'last_name' => $request->last_name];
        $context = ['theme_skin' => $request->theme_skin];
        $result = $this->guard()->user()->context()->update($context);
        if ($result) {
            Alert::success('Theme Skin Update')->flash();
        } else {
            Alert::error(trans('base.error_saving'))->flash();
        }

        return redirect()->back();
    }

    /**
     * Show the user a form to change his login password.
     */
    public function getChangePasswordForm()
    {
        $this->data['title'] = trans('base.my_account');
        $this->data['user'] = $this->guard()->user();

        return view('auth.account.change_password', $this->data);
    }

    /**
     * Save the new password for a user.
     */
    public function postChangePasswordForm(ChangePasswordRequest $request)
    {
        $user = $this->guard()->user();
        $user->password = Hash::make($request->new_password);

        if ($user->save()) {
            Alert::success(trans('base.account_updated'))->flash();
        } else {
            Alert::error(trans('base.error_saving'))->flash();
        }

        return redirect()->back();
    }

    /**
     * Get the guard to be used for account manipulation.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return auth();
    }
}
