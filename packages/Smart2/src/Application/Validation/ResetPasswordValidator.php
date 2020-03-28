<?php

namespace Smart2\Application\Validation;

use Illuminate\Support\Facades\Password;
use Session;

class ResetPasswordValidator
{
    /**
     * パスワード再登録時にsessionにemailが入っていてuserを取得できるかどうかの判定.
     *
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validateResetPasswordUser($attribute, $value, $parameters): bool
    {
        if (empty(Session::get('reset_password_email'))) {
            return false;
        }

        $email = Session::get('reset_password_email');
        $credentials = ['token' => $value];
        $credentials['email'] = $email;
        $broker = Password::broker('members');
        $user = $broker->getUser($credentials);

        if ($user === null) {
            return false;
        }

        return true;
    }

    /**
     * パスワード再登録時のtokenが有効かどうかの判定.
     *
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validateResetPasswordToken($attribute, $value, $parameters): bool
    {
        if (empty(Session::get('reset_password_email'))) {
            return false;
        }

        $email = Session::get('reset_password_email');
        $credentials = ['token' => $value];
        $credentials['email'] = $email;
        $broker = Password::broker('members');
        $user = $broker->getUser($credentials);

        if ($user === null) {
            return false;
        }

        if (!$broker->tokenExists($user, $credentials['token'])) {
            return false;
        }

        return true;
    }
}
