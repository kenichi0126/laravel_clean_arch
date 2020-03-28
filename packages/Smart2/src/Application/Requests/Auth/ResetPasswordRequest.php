<?php

namespace Smart2\Application\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'token' => 'bail|reset_password_token|reset_password_user',
            'password' => 'bail|required|min:7|max:20|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'token.reset_password_user' => 'ユーザー認証に失敗しました。お手数ですが改めて再設定メールを送信してください。',
            'token.reset_password_token' => 'パスワード再設定URLの有効期限がきれています。お手数ですが改めて再設定メールを送信してください。',
            'password.required' => '新しいパスワードを入力してください',
            'password.min' => 'パスワードは7文字以上入力してください',
            'password.max' => 'パスワードは20文字以内で入力してください',
            'password.confirmed' => '再入力パスワードと一致していません',
        ];
    }
}
