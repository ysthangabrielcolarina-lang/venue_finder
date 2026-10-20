<?php

namespace App\Http\Controllers;

use App\Http\Requests\VenueRequest;
use App\Models\Amenity;
use App\Models\Barangay;
use App\Models\Venue;
use Illuminate\Support\Facades\Gate;

class VenueController extends Controller
{
    public function index()
    {
        $venues = Venue::with('barangay')->withCount('owners')->orderBy('name')->paginate(10);

        return view('venues.index', compact('venues'));
    }

    public function show(Venue $venue)
    {
        $venue->load(['barangay', 'amenities']);

        return view('venues.show', compact('venue'));
    }

    public function create()
    {
        Gate::authorize('create', Venue::class);

        return view('venues.create', [
            'barangays' => Barangay::orderBy('name')->get(),
            'amenities' => Amenity::orderBy('name')->get(),
        ]);
    }

    public function store(VenueRequest $request)
    {
        Gate::authorize('create', Venue::class);

        $data = $request->validated();

        // $fillable on the model is the second layer of protection:
        // is_verified can never be set from this form.
        $venue = Venue::create(collect($data)->except('amenities')->all());
        $venue->amenities()->sync($data['amenities'] ?? []);

        if ($request->user()->role === 'owner') {
            $venue->owners()->attach($request->user()->id);
        }

        return redirect()->route('venues.show', $venue)->with('status', 'Venue created.');
    }

    public function edit(Venue $venue)
    {
        Gate::authorize('update', $venue);

        return view('venues.edit', [
            'venue'     => $venue->load('amenities'),
            'barangays' => Barangay::orderBy('name')->get(),
            'amenities' => Amenity::orderBy('name')->get(),
        ]);
    }

    public function update(VenueRequest $request, Venue $venue)
    {
        Gate::authorize('update', $venue);

        $data = $request->validated();

        $venue->update(collect($data)->except('amenities')->all());
        $venue->amenities()->sync($data['amenities'] ?? []);

        return redirect()->route('venues.show', $venue)->with('status', 'Venue updated.');
    }

    public function destroy(Venue $venue)
    {
        Gate::authorize('delete', $venue);

        $venue->delete();

        return redirect()->route('venues.index')->with('status', 'Venue deleted.');
    }
}