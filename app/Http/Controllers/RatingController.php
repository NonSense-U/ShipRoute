<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\StoreRatingRequest;
use App\Http\Resources\RatingCollection;
use App\Http\Resources\RatingResource;
use App\Models\Shipment;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use RatingService;

class RatingController extends Controller
{
    private RatingService $ratingService;

    public function __construct(RatingService $ratingService)
    {
        $this->ratingService = $ratingService;
    }

    public function store(StoreRatingRequest $request, Shipment $shipment)
    {
        $rating = $this->ratingService->rateShipmentCounterparty(
            $request->user(),
            $shipment,
            $request->validated()
        );

        return ApiResponse::success('Rating submitted successfully.', $rating, 201);
    }

    public function summary(User $user)
    {
        $summary = $this->ratingService->getRatingSummary($user);

        return ApiResponse::success('Rating summary retrieved successfully.', $summary);
    }

    public function getGivenRatings(Request $request)
    {   
        $ratings = $this->ratingService->getRatingsGiven($request->user());
        return ApiResponse::success(data: new RatingCollection($ratings));   
    }
}
