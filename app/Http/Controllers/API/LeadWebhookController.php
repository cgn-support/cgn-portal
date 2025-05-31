<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Carbon\Carbon;
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
        // Log the entire incoming request for debugging (optional, remove in production if too verbose)
        Log::info('Zoho Forms Webhook Received:', $request->all());

        // 1. Validate the incoming payload
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|string', // Ensure project_id exists in your projects table
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50', // Max length for phone numbers can vary
            'ip_address' => 'nullable|ip',
            'time_submitted' => 'required|string|date_format:d-M-Y H:i:s', // Validate the specific date format
            'session_id' => 'nullable|string|max:255',
            'referrer_name' => 'nullable|string', // No max length, as it's a text field in DB
            'initial_referrer' => 'nullable|string|max:255',
            'utm_source' => 'nullable|string|max:255',
            'utm_medium' => 'nullable|string|max:255',
            // Add validation for other UTM parameters if you expect them
            // 'utm_campaign' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            Log::error('Zoho Forms Webhook Validation Failed:', $validator->errors()->toArray());

            return response()->json(['status' => 'error', 'message' => 'Validation failed', 'errors' => $validator->errors()], 400);
        }

        $validatedData = $validator->validated();

        try {
            // 2. Parse the 'time_submitted' field
            // The format 'd-M-Y H:i:s' matches "18-May-2025 13:35:52"
            $submittedAt = Carbon::createFromFormat('d-M-Y H:i:s', $validatedData['time_submitted']);

            // 3. Create a new Lead
            $lead = new Lead;
            $lead->project_id = $validatedData['project_id'];
            $lead->first_name = $validatedData['first_name'] ?? null;
            $lead->last_name = $validatedData['last_name'] ?? null;
            $lead->email = $validatedData['email'] ?? null;
            $lead->phone = $validatedData['phone'] ?? null;
            $lead->ip_address = $validatedData['ip_address'] ?? null;
            $lead->submitted_at = $submittedAt; // Store the parsed Carbon instance
            $lead->session_id = $validatedData['session_id'] ?? null;
            $lead->referrer_name = $validatedData['referrer_name'] ?? null;
            $lead->initial_referrer = $validatedData['initial_referrer'] ?? null;
            $lead->utm_source = $validatedData['utm_source'] ?? null;
            $lead->utm_medium = $validatedData['utm_medium'] ?? null;

            // Store the full raw payload
            $lead->payload_data = $request->json()->all(); // or $request->all() if not always JSON

            // Default status is 'new' as per migration, so no need to set it explicitly unless you want to override
            // $lead->status = 'new';
            // $lead->is_valid = false; // Default is false

            $lead->save();

            Log::info('New Lead Created Successfully from Zoho Webhook:', ['lead_id' => $lead->id, 'project_id' => $lead->project_id]);

            // 4. Return a success response
            // Zoho Forms typically expects a 200 OK response.
            // You can return the created lead ID or a simple success message.
            return response()->json(['status' => 'success'], 201);

        } catch (\Exception $e) {
            // Log any other exceptions during the process
            Log::error('Error Processing Zoho Forms Webhook:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(), // Be cautious with trace in production logs (can be verbose)
                'payload' => $request->all(),
            ]);

            return response()->json(['status' => 'error', 'message' => 'An internal server error occurred.'], 500);
        }
    }
}
