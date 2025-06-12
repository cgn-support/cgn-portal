<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LeadWebhookController extends Controller
{
    /**
     * Handle incoming webhook from Zoho Forms.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request)
    {
        // Log the entire incoming request for debugging
        Log::info('Zoho Forms Webhook Received:', $request->all());

        // 1. Basic validation - only validate what's essential
        $validator = Validator::make($request->all(), [
            'project_id' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (!Project::where('id', $value)->exists()) {
                        $fail('The project_id does not exist in our system.');
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            Log::error('Zoho Forms Webhook Validation Failed:', [
                'errors' => $validator->errors()->toArray(),
                'payload' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            // 2. Create lead using our static method
            $lead = Lead::createFromWebhook($request->all());

            Log::info('New Lead Created Successfully from Zoho Webhook:', [
                'lead_id' => $lead->id,
                'project_id' => $lead->project_id,
                'email' => $lead->email,
                'name' => $lead->name
            ]);

            // 3. Optional: Trigger notifications or other actions here
            // $this->notifyAccountManager($lead);
            // $this->sendAutoResponder($lead);

            // 4. Return success response
            return response()->json([
                'status' => 'success',
                'lead_id' => $lead->id
            ], 201);
        } catch (\InvalidArgumentException $e) {
            Log::error('Invalid Zoho Forms Webhook Data:', [
                'message' => $e->getMessage(),
                'payload' => $request->all(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            Log::error('Error Processing Zoho Forms Webhook:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'payload' => $request->all(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'An internal server error occurred.'
            ], 500);
        }
    }

    /**
     * Optional: Send notification to account manager
     */
    private function notifyAccountManager(Lead $lead): void
    {
        try {
            $project = $lead->project;
            $accountManager = $project->accountManager;

            if ($accountManager) {
                // Send notification (email, Slack, etc.)
                // Notification::send($accountManager, new NewLeadNotification($lead));
                Log::info('Account Manager Notified:', [
                    'lead_id' => $lead->id,
                    'account_manager' => $accountManager->email
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to notify account manager:', [
                'lead_id' => $lead->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Optional: Send auto-responder to lead
     */
    private function sendAutoResponder(Lead $lead): void
    {
        try {
            if ($lead->email) {
                // Send auto-responder email
                // Mail::to($lead->email)->send(new LeadAutoResponder($lead));
                Log::info('Auto-responder sent:', [
                    'lead_id' => $lead->id,
                    'email' => $lead->email
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('Failed to send auto-responder:', [
                'lead_id' => $lead->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
