<?php

namespace App\Actions\Fortify;

use App\Models\User\InvitationCode;
use App\Models\User\Rank;
use App\Models\User\User;
use App\Services\InvitationService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers {
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param array<string, string> $input
     */
    public function create(array $input): User {
        Validator::make($input, [
            'name'  => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique(User::class)],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password'  => $this->passwordRules(),
            'agreement' => ['required', 'accepted'],
            'code'      => ['string', function ($attribute, $value, $fail) {
                $invitation = InvitationCode::where('code', $value)->whereNull('recipient_id')->first();
                if (!$invitation) {
                    $fail('Invalid code entered.');
                }
            },
            ],
        ])->validate();

        $user = User::create([
            'name'     => $input['name'],
            'email'    => $input['email'],
            'password' => Hash::make($input['password']),
            'rank_id'  => Rank::orderBy('sort', 'ASC')->first()->id,
        ]);

        if (!(new InvitationService)->useInvitation(InvitationCode::where('code', $input['code'])->whereNull('recipient_id')->first(), $user)) {
            throw new \Exception('An error occurred while using the invitation code.');
        }

        return $user;
    }
}
