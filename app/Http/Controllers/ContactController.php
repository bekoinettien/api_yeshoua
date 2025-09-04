<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
        'name'    => 'required|string|max:255',
        'email'   => 'required|email',
        'gender'  => 'required|string|max:10',
        'subject' => 'required|string|max:255',
        'message' => 'required|string',
    ]);

    $data = $request->only('name', 'email', 'gender', 'subject', 'message');

    // ✉️ Envoi de l'email sans vue
    Mail::raw("
        Nouveau message de contact :

        Nom     : {$data['name']}
        Email   : {$data['email']}
        Genre   : {$data['gender']}
        Sujet   : {$data['subject']}
        Message : {$data['message']}
    ", function ($mail) use ($data) {
        $mail->to('templatehtmlcssjss@gmail.com') // 👉 email de l'admin
             ->subject('Nouveau message de contact : ' . $data['subject']);
    });

    return response()->json([
        'message' => 'Votre message a été envoyé avec succès ✅'
    ], 200);

}
}
