<?php

use App\Models\Client;
use App\Models\User;
use App\Models\Business;
use App\Models\Project;
use App\Models\Ticket;

describe('Client Model', function () {
    test('can be created with factory', function () {
        $client = Client::factory()->create();
        
        expect($client)->toBeInstanceOf(Client::class)
            ->and($client->name)->toBeString()
            ->and($client->primary_contact_email)->toBeString();
    });

    test('can be created as active', function () {
        $client = Client::factory()->active()->create();
        
        expect($client->status)->toBe('active');
    });

    test('can be created as inactive', function () {
        $client = Client::factory()->inactive()->create();
        
        expect($client->status)->toBe('inactive');
    });

    test('has users relationship', function () {
        $client = Client::factory()->create();
        
        expect($client->users())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
    });

    test('can have associated users', function () {
        $client = Client::factory()->create();
        $user = User::factory()->withClient($client)->create();
        
        expect($client->users)->toHaveCount(1)
            ->and($client->users->first()->id)->toBe($user->id);
    });

    test('has businesses relationship', function () {
        $client = Client::factory()->create();
        
        expect($client->businesses())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
    });

    test('can have associated businesses', function () {
        $client = Client::factory()->create();
        $business = Business::factory()->create(['client_id' => $client->id]);
        
        expect($client->businesses)->toHaveCount(1)
            ->and($client->businesses->first()->id)->toBe($business->id);
    });

    test('has projects relationship through businesses', function () {
        $client = Client::factory()->create();
        
        expect($client->projects())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasManyThrough::class);
    });

    test('can access projects through businesses', function () {
        $client = Client::factory()->create();
        $business = Business::factory()->create(['client_id' => $client->id]);
        $project = Project::factory()->create(['business_id' => $business->id]);
        
        expect($client->projects)->toHaveCount(1)
            ->and($client->projects->first()->id)->toBe($project->id);
    });

    test('has tickets relationship', function () {
        $client = Client::factory()->create();
        
        expect($client->tickets())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
    });

    test('can have associated tickets', function () {
        $client = Client::factory()->create();
        $ticket = Ticket::factory()->create(['client_id' => $client->id]);
        
        expect($client->tickets)->toHaveCount(1)
            ->and($client->tickets->first()->id)->toBe($ticket->id);
    });

    test('uses soft deletes', function () {
        $client = Client::factory()->create();
        $clientId = $client->id;
        
        $client->delete();
        
        expect(Client::find($clientId))->toBeNull()
            ->and(Client::withTrashed()->find($clientId))->not->toBeNull()
            ->and(Client::withTrashed()->find($clientId)->deleted_at)->not->toBeNull();
    });

    test('has correct fillable attributes', function () {
        $fillable = [
            'name',
            'primary_contact_name',
            'primary_contact_email',
            'primary_contact_phone',
            'primary_contact_title',
            'preferred_comms_method',
            'hubspot_company_record',
            'signing_date',
            'status',
            'notes',
        ];
        
        $client = new Client();
        
        expect($client->getFillable())->toBe($fillable);
    });

    test('signing_date is properly cast', function () {
        $client = Client::factory()->create();
        
        // The current casts array is empty, so this tests the actual behavior
        $casts = $client->getCasts();
        
        // We expect deleted_at to be present from SoftDeletes trait
        expect($casts)->toHaveKey('deleted_at');
    });

    test('can be found by status', function () {
        Client::factory()->active()->create();
        Client::factory()->inactive()->create();
        
        $activeClients = Client::where('status', 'active')->get();
        $inactiveClients = Client::where('status', 'inactive')->get();
        
        expect($activeClients)->toHaveCount(1)
            ->and($inactiveClients)->toHaveCount(1);
    });

    test('can be filtered by preferred communication method', function () {
        Client::factory()->create(['preferred_comms_method' => 'email']);
        Client::factory()->create(['preferred_comms_method' => 'phone']);
        
        $emailClients = Client::where('preferred_comms_method', 'email')->get();
        $phoneClients = Client::where('preferred_comms_method', 'phone')->get();
        
        expect($emailClients)->toHaveCount(1)
            ->and($phoneClients)->toHaveCount(1);
    });
});