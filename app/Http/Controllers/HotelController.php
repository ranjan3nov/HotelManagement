<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    // Add Hotel API - POST Method
    public function addHotel(Request $request)
    {
        // Validate input data
        $request->validate([
            'name' => 'required|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        // Save hotel to database
        $hotel = new Hotel;
        $hotel->name = $request->input('name');
        $hotel->latitude = $request->input('latitude');
        $hotel->longitude = $request->input('longitude');
        $hotel->save();

        // Return success response
        return response()->json(['message' => 'Hotel added successfully'], 201);
    }

    // Get Nearby Hotels API - GET Method
    public function getNearbyHotels(Request $request)
    {
        // Validate input data
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        // Define search radius (in kilometers)
        $radius = 10;

        // Calculate bounding box for search area
        $minLat = $request->input('latitude') - ($radius / 111.2);
        $maxLat = $request->input('latitude') + ($radius / 111.2);
        $minLng = $request->input('longitude') - ($radius / (111.2 * cos(deg2rad($request->input('latitude')))));
        $maxLng = $request->input('longitude') + ($radius / (111.2 * cos(deg2rad($request->input('latitude')))));

        // Retrieve nearby hotels from database
        $hotels = Hotel::whereBetween('latitude', [$minLat, $maxLat])
            ->whereBetween('longitude', [$minLng, $maxLng])
            ->get();

        // Return success response
        return response()->json(['hotels' => $hotels]);
    }
}
