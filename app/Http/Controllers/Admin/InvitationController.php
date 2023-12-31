<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User\InvitationCode;
use App\Services\InvitationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvitationController extends Controller {
    /**
     * Shows the invitation key index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex(Request $request) {
        return view('admin.users.invitations', [
            'invitations' => InvitationCode::with('user', 'recipient')->orderBy('id', 'DESC')->paginate(20)->appends($request->query()),
        ]);
    }

    /**
     * Generates a new invitation key.
     *
     * @param App\Services\InvitationService $service
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postGenerateKey(InvitationService $service) {
        if ($service->generateInvitation(Auth::user())) {
            flash('Generated invitation successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                $service->addError($error);
            }
        }

        return redirect()->back();
    }

    /**
     * Generates a new invitation key.
     *
     * @param App\Services\InvitationService $service
     * @param int                            $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteKey(InvitationService $service, $id) {
        $invitation = InvitationCode::find($id);
        if ($invitation && $service->deleteInvitation($invitation)) {
            flash('Deleted invitation key successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                $service->addError($error);
            }
        }

        return redirect()->back();
    }
}
