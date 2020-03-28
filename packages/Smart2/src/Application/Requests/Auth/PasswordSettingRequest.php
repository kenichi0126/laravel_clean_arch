<?php

namespace Smart2\Application\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class PasswordSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'password' => 'required|min:7|max:20|confirmed',
            'password_confirmation' => 'required|min:7|max:20',
        ];
    }

    public function messages()
    {
        return [
            'password.required' => '新しいパスワードを入力してください',
            'password.min' => 'パスワードは7文字以上入力してください',
            'password.max' => 'パスワードは20文字以内で入力してください',
            'password.confirmed' => '再入力パスワードと一致していません',
            'password_confirmation.required' => '新しいパスワードの再入力を入力してください',
            'password_confirmation.min' => 'パスワードは7文字以上入力してください',
            'password_confirmation.man' => 'パスワードは20文字以内で入力してください',
        ];
    }
}
