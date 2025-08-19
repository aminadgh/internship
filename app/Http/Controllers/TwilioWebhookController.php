<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MessageRecipient;
use Illuminate\Support\Facades\Log;

class TwilioWebhookController extends Controller
{
    /**
     * Handle Twilio webhook for message status updates
     */
    public function handleStatusCallback(Request $request)
    {
        // Verify this is a legitimate Twilio request (you can add more verification)
        $twilioSignature = $request->header('X-Twilio-Signature');
        
        Log::info('Twilio webhook received', [
            'message_sid' => $request->input('MessageSid'),
            'message_status' => $request->input('MessageStatus'),
            'signature' => $twilioSignature ? 'present' : 'missing'
        ]);

        $messageSid = $request->input('MessageSid');
        $messageStatus = $request->input('MessageStatus');
        $errorCode = $request->input('ErrorCode');
        $errorMessage = $request->input('ErrorMessage');

        if (!$messageSid || !$messageStatus) {
            Log::warning('Invalid webhook data received', $request->all());
            return response('Invalid data', 400);
        }

        // Find the message recipient by Twilio SID
        $recipient = MessageRecipient::where('twilio_sid', $messageSid)->first();

        if (!$recipient) {
            Log::warning('Message recipient not found for Twilio SID', ['twilio_sid' => $messageSid]);
            return response('Recipient not found', 404);
        }

        // Update the recipient status
        $oldStatus = $recipient->status;
        $newStatus = MessageRecipient::TWILIO_STATUS_MAP[$messageStatus] ?? $messageStatus;

        $updateData = [
            'twilio_status' => $messageStatus,
            'status' => $newStatus,
            'last_attempt_at' => now(),
        ];

        // Set delivered_at timestamp when message is delivered
        if (in_array($messageStatus, ['delivered'])) {
            $updateData['delivered_at'] = now();
        }

        // Set read_at timestamp when message is read (if Twilio provides this)
        if (in_array($messageStatus, ['read'])) {
            $updateData['read_at'] = now();
        }

        // Handle error cases
        if (in_array($messageStatus, ['failed', 'undelivered']) && $errorCode) {
            $updateData['error_message'] = "Twilio Error {$errorCode}: {$errorMessage}";
        }

        $recipient->update($updateData);

        Log::info('Message recipient status updated', [
            'recipient_id' => $recipient->id,
            'message_id' => $recipient->message_id,
            'contact_id' => $recipient->contact_id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'twilio_status' => $messageStatus,
            'error_code' => $errorCode,
            'error_message' => $errorMessage
        ]);

        // Dispatch event for real-time updates (if using broadcasting)
        // event(new MessageStatusUpdated($recipient));

        return response('OK', 200);
    }

    /**
     * Handle Twilio webhook for delivery receipts
     */
    public function handleDeliveryReceipt(Request $request)
    {
        Log::info('Twilio delivery receipt received', $request->all());

        $messageSid = $request->input('MessageSid');
        $deliveryStatus = $request->input('DeliveryStatus');

        if (!$messageSid || !$deliveryStatus) {
            return response('Invalid data', 400);
        }

        $recipient = MessageRecipient::where('twilio_sid', $messageSid)->first();

        if (!$recipient) {
            return response('Recipient not found', 404);
        }

        // Update delivery status
        $recipient->update([
            'twilio_status' => $deliveryStatus,
            'status' => MessageRecipient::TWILIO_STATUS_MAP[$deliveryStatus] ?? $deliveryStatus,
            'delivered_at' => $deliveryStatus === 'delivered' ? now() : null,
        ]);

        return response('OK', 200);
    }
}
