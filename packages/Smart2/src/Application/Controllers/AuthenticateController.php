<?php

namespace Smart2\Application\Controllers;

use Carbon\Carbon;
use DB;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Password;
use Session;
use Smart2\Application\Requests\Auth\ChangePasswordRequest;
use Smart2\Application\Requests\Auth\PasswordSettingRequest;
use Smart2\Application\Requests\Auth\PrepareResetPassword;
use Smart2\Application\Requests\Auth\ResetPasswordRequest;
use Smart2\Application\Requests\Auth\SendResetLinkEmailRequest;
use Smart2\Application\Services\LoginFailed\LoginFailedService;
use Smart2\Application\Services\ResetPasswordService;
use Smart2\Application\Services\UserInfoService;
use Smart2\CommandModel\Eloquent\MemberAccess;
use Smart2\CommandModel\Eloquent\MemberLoginLog;

class AuthenticateController extends Controller
{
    use SendsPasswordResetEmails;

    protected $userInfoService;

    protected $loginFailedService;

    protected $resetPasswordService;

    public function __construct(
        UserInfoService $userInfoService,
        LoginFailedService $loginFailedService,
        ResetPasswordService $resetPasswordService
    ) {
        $this->userInfoService = $userInfoService;
        $this->loginFailedService = $loginFailedService;
        $this->resetPasswordService = $resetPasswordService;
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (!auth()->attempt($credentials)) {
            $this->loginFailedService->add($request->email);
            abort(422, 'invalid credentials');
        }

        if (!auth()->user()->isValidContract()) {
            abort(403);
        }

        try {
            $user = $this->userInfoService->execute(auth()->id());
        } catch (\Exception $e) {
            abort(422, 'invalid credentials');
        }

        $userId = $user->id;
        $now = Carbon::now();
        $token = md5($userId . $now->format('Ymdhisu'));

        DB::transaction(function () use ($userId, $now, $token): void {
            $memberAccess = MemberAccess::findOrFail($userId);
            $memberAccess->fill([
                'login_count' => ++$memberAccess->login_count,
                'last_login_at' => $now,
                'login_token' => $token,
            ])->save();

            MemberLoginLog::create([
                'member_id' => $userId,
                'created_at' => $now,
            ]);
        });

        Session::put('login_token', $token);

        return response()->json($user);
    }

    public function logout(): Response
    {
        auth()->logout();

        return response()->make()->setStatusCode(204);
    }

    public function me(): JsonResponse
    {
        if (!auth()->user()->isValidContract()) {
            abort(403);
        }

        $user = $this->userInfoService->execute(auth()->id());

        return response()->json($user);
    }

    public function changePassword(ChangePasswordRequest $request): Response
    {
        $requestDatas = $request->only('old_password', 'password', 'password_confirmation');

        $credentials = ['email' => auth()->user()->email, 'password' => $requestDatas['old_password']];

        if (!auth()->validate($credentials)) {
            return response()->make()->setStatusCode(421);
        }
        $this->userInfoService->changePassword(auth()->id(), $requestDatas);
        return response()->make()->setStatusCode(204);
    }

    public function initLoginChangePassword(PasswordSettingRequest $request): Response
    {
        $requestDatas = $request->only('password', 'password_confirmation');
        $this->userInfoService->changePassword(auth()->id(), $requestDatas);
        return response()->make()->setStatusCode(204);
    }

    public function resetPassword(ResetPasswordRequest $request): Response
    {
        $this->resetPasswordService->changePassword($request, $this->broker());
        return response()->make()->setStatusCode(204);
    }

    // パスワード再設定準備
    public function prepareResetPassword(PrepareResetPassword $request): JsonResponse
    {
        $email = Session::get('reset_password_email');
        return response()->json(['email' => $email]);
    }

    public function sendResetLinkEmail(SendResetLinkEmailRequest $request): JsonResponse
    {
        $response = $this->broker()->sendResetLink(
            $request->only('email')
        );
        Session::put('reset_password_email', $request->email);
        return response()->json(['response' => $response]);
    }

    /**
     *パスワードリセットに使われるブローカの取得.
     *
     * @return PasswordBroker
     */
    protected function broker(): PasswordBroker
    {
        return Password::broker('members');
    }
}
