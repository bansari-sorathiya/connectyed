<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\NewsletterSubscriber;
use App\Notifications\NewsletterConfirmation;

class NewsletterController extends Controller
{
    /**
     * Subscribe to the newsletter.
     */
    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:newsletter_subscribers,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid data',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Save subscriber
        $subscriber = NewsletterSubscriber::create([
            'email' => $request->email,
        ]);

        // Send confirmation email
        $subscriber->notify(new NewsletterConfirmation($subscriber->email));

        return response()->json([
            'success' => true,
            'message' => 'Subscribed successfully! Please check your email to confirm your subscription.',
        ], 200);
    }

    /**
     * Confirm the newsletter subscription.
     */
    public function confirm(Request $request)
    {
        $email = $request->query('email');

        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect('/')->with('error', 'Invalid confirmation link.');
        }

        $subscriber = NewsletterSubscriber::where('email', $email)->first();

        if (!$subscriber) {
            return redirect('/')->with('error', 'Subscription not found.');
        }

        if ($subscriber->confirmed) {
            return redirect('/')->with('info', 'Your subscription is already confirmed.');
        }

        $subscriber->update(['confirmed' => true]);

        return redirect('/')->with('success', 'Your subscription has been confirmed!');
    }
    public function unsubscribe(Request $request)
{
    $email = $request->query('email');

    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return redirect('/')->with('error', 'Invalid unsubscription link.');
    }

    $subscriber = NewsletterSubscriber::where('email', $email)->first();

    if (!$subscriber) {
        return redirect('/')->with('error', 'Subscription not found.');
    }

    $subscriber->delete();

    return redirect('/')->with('success', 'You have been unsubscribed from the newsletter.');
}

}
