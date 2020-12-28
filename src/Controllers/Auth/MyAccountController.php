<?php

namespace Sagartakle\Laracrud\Controllers\Auth;

use Alert;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Http\Requests\AccountInfoRequest;
use Illuminate\Support\Facades\Hash;
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
        $this->data['crud'] = \Module::make('Users');
        $this->data['crud']->datatable = true;
        if(isset(\Module::user()->id) && isset(\Module::user()->context()->id)) {
            $this->data['crud']->row = \Module::user()->context();
        }
        
        return view(config('stlc.view_path.auth.account.update_info','stlc::auth.account.update_info'), $this->data);
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

        return view(config('stlc.view_path.auth.account.change_password','stlc::auth.account.change_password'), $this->data);
    }

    /**
     * Save the new password for a user.
     */
    public function postChangePasswordForm(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'old_password'     => 'required',
            'new_password'     => 'required|min:4',
            'confirm_password' => 'required|same:new_password|min:4',
        ])->after(function ($validator) use($request) {
            if (! Hash::check($request->old_password, auth()->user()->password)) {
                $validator->errors()->add('old_password', 'Old password incorrect');
            }
        });
        if ($validator->fails()) {
            if(isset($data->src_ajax)) {
                return response()->json(['status' => 'validation_error', 'message' => 'Validation Error', 'errors' => $validator->getMessage()]);
            } else {
                return redirect()->back()->withErrors($validator)->withInput();
            }
        }

        $user = $this->guard()->user();
        $user->password = Hash::make($request->new_password);

        if ($user->save()) {
            Alert::success('Password Updated')->flash();
        } else {
            Alert::error('Error Saving')->flash();
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
