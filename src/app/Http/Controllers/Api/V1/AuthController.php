<?php

namespace App\Http\Controllers\Api\V1;

use Carbon\Carbon;
use Different\Dwfw\app\Traits\LoggableApi;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Api\BaseApiFormRequest;
use App\Http\Requests\Api\V1\AuthLoginRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\Api\V1\AuthRegisterRequest;
use App\Http\Requests\Api\V1\AuthLostPasswordRequest;
use App\Http\Requests\Api\V1\AuthRegisterConfirmRequest;
use App\Http\Requests\Api\V1\AuthPasswordRecoveryRequest;
use App\Http\Requests\Api\V1\AuthSaveNewPasswordRequest;
use App\Models\PasswordReset;
use App\Models\User;

class AuthController extends Controller
{
    use LoggableApi;

    public function login(AuthLoginRequest $request)
    {
        $credentials = [
            'email' => $request->validated()['email'],
            'password' => $request->validated()['password'],
        ];

        if (!Auth::attempt($credentials)) {
            $this->log('AUTH_LOGIN_FAILED', null, $request->validated()['email'], User::class, null);
            BaseApiFormRequest::throwError('Hibás e-mail cím vagy jelszó!');
        }

        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        $token->save();

        $this->log('AUTH_LOGIN', $user->id, $user, User::class, $user->id);

        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ]);
    }

    public function register(AuthRegisterRequest $request)
    {
        $user = new User;
        $user->name = $request->validated()['name'];
        $user->email = $request->validated()['email'];
        $user->password = Hash::make($request->validated()['password']);
        $user->email_verify_pin = substr(str_shuffle("0123456789"), 0, 6);
        $user->save();

        $user->sendEmailVerificationNotification();

        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        $token->save();

        $this->log('AUTH_REGISTER', $user->id, $user, User::class, $user->id);

        return response()->json([
            'error' => false,
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ]);
    }

    public function logout(Request $request)
    {
        // és most lehet a sima tokent is
        $request->user()->token()->revoke();

        $this->log('AUTH_LOGOUT', $request->user()->id, $request->user(), User::class, $request->user()->id);

        return response()->json([
            'error' => false,
            'message' => 'Sikeres kijelentkezés.'
        ]);
    }

    public function registerConfirm(AuthRegisterConfirmRequest $request)
    {
        if ($request->user()->email_verified_at != null) {
            $this->log('AUTH_REG_CONFIRMED_ALREADY', $request->user()->id, $request->user(), User::class, $request->user()->id);
            BaseApiFormRequest::throwError('A felhasználó már meg lett erősítve.');
        }

        if ($request->user()->email_verify_pin != $request->validated()['pin']) {
            $this->log('AUTH_REG_CONFIRMED_BAD_PIN', 'v1/register-confirm', $request->user()->id, $request->user(), User::class, $request->user()->id);
            BaseApiFormRequest::throwError('Hibás PIN kód.');
        }
        $request->user()->markEmailAsVerified();

        $this->log('AUTH_REG_CONFIRMED', $request->user()->id, $request->user(), User::class, $request->user()->id);

        return response()->json([
            'error' => false,
            'message' => 'Felhasználó megerősítve.',
            'date' => Carbon::now(),
        ]);
    }

    public function newPin(Request $request)
    {
        $user = $request->user();

        if ($user->email_verified_at != null) {
            $this->log('AUTH_REG_CONFIRMED_ALREADY', $user->id, $user, User::class, $user->id);
            BaseApiFormRequest::throwError('A felhasználó már meg lett erősítve.');
        }

        $user->email_verify_pin = substr(str_shuffle("0123456789"), 0, 6);
        $user->save();

        $user->sendEmailVerificationNotification();

        $this->log('AUTH_REG_NEW_PIN_SENT', $user->id, $user, User::class, $user->id);

        return response()->json([
            'error' => false,
            'message' => 'Új kód kiküldve.',
        ]);
    }

    public function lostPassword(AuthLostPasswordRequest $request)
    {
        $validated = $request->validated();

        try {
            $user = User::query()->where('email', $validated['email'])->firstOrFail();
        } catch (ModelNotFoundException $ex) {
            $this->log('AUTH_LOST_PASSWORD_MISSING_EMAIL', null, $validated['email'], User::class, null, 'OK');
            BaseApiFormRequest::throwError('Az e-mail cím nem található a rendszerünkben.');
        }

        $token = substr(str_shuffle("0123456789"), 0, 8);
        $user->sendPasswordResetNotification($token);

        $passwordReset = new PasswordReset;
        $passwordReset->token = $token;
        $passwordReset->email = $validated['email'];
        $passwordReset->save();

        $this->log('AUTH_LOST_PASSWORD_SENT', $user->id, $user, User::class, $user->id);

        return response()->json([
            'error' => false,
            'message' => 'Jelszó emlékeztető kiküldve.'
        ]);
    }

    public function passwordRecovery(AuthPasswordRecoveryRequest $request)
    {
        $passwordReset = PasswordReset::where('token', $request->validated()['hash'])->first();
        if (!$passwordReset || Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
            $this->log('AUTH_PASSWORD_FAILED', null, $request->validated()['email'], PasswordReset::class, null);
            BaseApiFormRequest::throwError('A jelszó visszaállítás sikertelen. Kérjen új jelszó visszaállítást.');
        }

        try {
            $user = User::where('email', $request->validated()['email'])->firstOrFail();
        } catch (ModelNotFoundException $ex) {
            $this->log('AUTH_PASSWORD_FAILED', null, $request->validated()['email'], PasswordReset::class, null);
            BaseApiFormRequest::throwError('Az e-mail cím nem található a rendszerünkben.');
        }

        $user->password = Hash::make($request->validated()['password']);
        $user->save();

        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        $token->save();

        $passwordReset->delete();

        $this->log('AUTH_PASSWORD_MODIFIED', $user->id, $user, User::class, $user->id);

        return response()->json([
            'error' => false,
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ]);
    }

    public function user(Request $request)
    {
        return response()->json([
            'error' => false,
            'data' => $request->user(),
        ]);
    }
}
