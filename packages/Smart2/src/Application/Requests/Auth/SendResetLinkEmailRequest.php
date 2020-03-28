<?php

namespace Smart2\Application\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class SendResetLinkEmailRequest extends FormRequest
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
            'email' => 'required|email',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'メールアドレスを入力してください。 ',
            'email.email' => '有効なメールアドレスを入力してください。 ',
        ];
    }
}
