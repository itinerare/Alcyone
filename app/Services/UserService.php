<?php

namespace App\Services;

use App\Models\User\Rank;
use App\Models\User\User;
use App\Models\User\UserUpdateLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;

class UserService extends Service {
    /*
    |--------------------------------------------------------------------------
    | User Service
    |--------------------------------------------------------------------------
    |
    | Handles the creation and editing of users.
    |
    */

    /**
     * Create a user.
     *
     * @param array $data
     *
     * @return User
     */
    public function createUser($data) {
        // If the rank is not given, create a user with the lowest existing rank.
        if (!isset($data['rank_id'])) {
            $data['rank_id'] = Rank::orderBy('sort')->first()->id;
        }

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'rank_id'  => $data['rank_id'],
            'password' => Hash::make($data['password']),
        ]);

        return $user;
    }

    /**
     * Updates a user. Used in modifying the admin user on the command line.
     *
     * @param array $data
     *
     * @return User
     */
    public function updateUser($data) {
        $user = User::find($data['id']);
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        if (isset($data['email']) && $user && $data['email'] != $user->email) {
            $user->forceFill(['email_verified_at' => null]);
            $data['email_old'] = $user->email;
        }

        if ($user) {
            $user->update($data);
            if (isset($data['email_old'])) {
                $$user->sendEmailVerificationNotification();
            }
        }

        return $user;
    }

    /**
     * Updates the user's password.
     *
     * @param array $data
     * @param User  $user
     *
     * @return bool
     */
    public function updatePassword($data, $user) {
        DB::beginTransaction();

        try {
            if (!Hash::check($data['old_password'], $user->password)) {
                throw new \Exception('Please enter your old password.');
            }
            if (Hash::make($data['new_password']) == $user->password) {
                throw new \Exception('Please enter a different password.');
            }

            $user->password = Hash::make($data['new_password']);
            $user->save();

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Updates the user's email and resends a verification email.
     *
     * @param array $data
     * @param User  $user
     *
     * @return bool
     */
    public function updateEmail($data, $user) {
        $user->email = $data['email'];
        $user->email_verified_at = null;
        $user->save();

        $user->sendEmailVerificationNotification();

        return true;
    }

    /**
     * Confirms a user's two-factor auth.
     *
     * @param string           $code
     * @param array            $data
     * @param \App\Models\User $user
     *
     * @return bool
     */
    public function confirmTwoFactor($code, $data, $user) {
        DB::beginTransaction();

        try {
            if (app(TwoFactorAuthenticationProvider::class)->verify(decrypt($data['two_factor_secret']), $code['code'])) {
                $user->forceFill([
                    'two_factor_secret'         => $data['two_factor_secret'],
                    'two_factor_recovery_codes' => $data['two_factor_recovery_codes'],
                ])->save();
            } else {
                throw new \Exception('Provided code was invalid.');
            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Disables a user's two-factor auth.
     *
     * @param string           $code
     * @param \App\Models\User $user
     *
     * @return bool
     */
    public function disableTwoFactor($code, $user) {
        DB::beginTransaction();

        try {
            if (app(TwoFactorAuthenticationProvider::class)->verify(decrypt($user->two_factor_secret), $code['code'])) {
                $user->forceFill([
                    'two_factor_secret'         => null,
                    'two_factor_recovery_codes' => null,
                ])->save();
            } else {
                throw new \Exception('Provided code was invalid.');
            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Bans a user.
     *
     * @param array $data
     * @param User  $user
     * @param User  $staff
     *
     * @return bool
     */
    public function ban($data, $user, $staff) {
        DB::beginTransaction();

        try {
            if (!$user->is_banned) {
                // For a new ban, create an update log
                UserUpdateLog::create(['staff_id' => $staff->id, 'user_id' => $user->id, 'data' => json_encode(['is_banned' => 'Yes', 'ban_reason' => $data['ban_reason'] ?? null]), 'type' => 'Ban']);

                $user->banned_at = Carbon::now();

                $user->is_banned = 1;
                $user->rank_id = Rank::orderBy('sort', 'ASC')->first()->id;
                $user->save();
            } else {
                UserUpdateLog::create(['staff_id' => $staff->id, 'user_id' => $user->id, 'data' => json_encode(['ban_reason' => $data['ban_reason'] ?? null]), 'type' => 'Ban Update']);
            }

            $user->ban_reason = isset($data['ban_reason']) && $data['ban_reason'] ? $data['ban_reason'] : null;
            $user->save();

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Unbans a user.
     *
     * @param User $user
     * @param User $staff
     *
     * @return bool
     */
    public function unban($user, $staff) {
        DB::beginTransaction();

        try {
            if ($user->is_banned) {
                $user->is_banned = 0;
                $user->ban_reason = null;
                $user->banned_at = null;
                $user->save();

                UserUpdateLog::create(['staff_id' => $staff->id, 'user_id' => $user->id, 'data' => json_encode(['is_banned' => 'No']), 'type' => 'Unban']);
            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }
}
