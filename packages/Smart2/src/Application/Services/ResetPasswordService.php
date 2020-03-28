<?php

namespace Smart2\Application\Services;

use Carbon\Carbon;
use Hash;
use Illuminate\Contracts\Auth\PasswordBroker;
use Session;
use Smart2\Application\Requests\Auth\ResetPasswordRequest;
use Smart2\CommandModel\Eloquent\Member;
use Smart2\CommandModel\Eloquent\ResetPasswordHistory;

class ResetPasswordService
{
    protected $resetPasswordHistory;

    public function __construct(ResetPasswordHistory $resetPasswordHistory)
    {
        $this->resetPasswordHistory = $resetPasswordHistory;
    }

    public function changePassword(ResetPasswordRequest $request, PasswordBroker $broker): void
    {
        $email = Session::get('reset_password_email');

        $credentials = $request->only('password', 'password_confirmation', 'token');
        $credentials['email'] = $email;
        $user = $broker->getUser($credentials);

        $id = $user->getOriginal()['id'];
        $password = $credentials['password'];

        $now = Carbon::now();

        $member = Member::find($id);
        $member->password_digest = Hash::make($password);
        $member->save();

        $this->resetPasswordHistory->member_id = $id;
        $this->resetPasswordHistory->created_at = $now;
        $this->resetPasswordHistory->save();

        $broker->deleteToken($user);
    }
}
