<?php

namespace App\Services;

use App\Models\Rating;
use App\Models\Shipment;
use App\Models\User;
use RuntimeException;

class RatingService
{
	public function rateShipmentCounterparty(User $rater, Shipment $shipment, array $payload): Rating
	{
		$shipment->loadMissing(['merchant.user', 'driver.user']);

		if ($shipment->status !== 'delivered') {
			throw new RuntimeException('Shipment must be delivered before rating.');
		}

		$ratee = $this->resolveRateeUser($rater, $shipment);

		$alreadyRated = Rating::query()
			->where('shipment_id', $shipment->id)
			->where('rater_id', $rater->id)
			->exists();

		if ($alreadyRated) {
			throw new RuntimeException('You have already rated this shipment.');
		}

		if ($ratee->id === $rater->id) {
			throw new RuntimeException('You cannot rate yourself.');
		}

		return Rating::create([
			'shipment_id' => $shipment->id,
			'rater_id' => $rater->id,
			'ratee_id' => $ratee->id,
			'rating' => $payload['rating'],
			'comment' => $payload['comment'] ?? null,
		]);
	}

	public function getRatingSummary(User $user): array
	{
		$stats = $user->ratingsReceived()
			->selectRaw('COUNT(*) as total, AVG(rating) as average')
			->first();

		return [
			'user_id' => $user->id,
			'average_rating' => $stats?->average !== null ? round((float) $stats->average, 2) : 0.0,
			'ratings_count' => (int) ($stats?->total ?? 0),
		];
	}


	public function getRatingsReceived(User $user)
	{
		return $user->ratingsReceived()->with('ratee', 'rater')->paginate(20);
	}

	public function getRatingsGiven(User $user)
	{
		return $user->ratingsGiven()->with('ratee', 'rater')->paginate(20);
	}

	private function resolveRateeUser(User $rater, Shipment $shipment): User
	{
		if ($rater->hasRole('merchant')) {
			if (!$shipment->merchant || $shipment->merchant->user_id !== $rater->id) {
				throw new RuntimeException('You are not assigned to this shipment as a merchant.');
			}
			$shipment->update(['rated_by_merchant' => true]);
			$ratee = $shipment->driver?->user;
		} elseif ($rater->hasRole('driver')) {
			if (!$shipment->driver || $shipment->driver->user_id !== $rater->id) {
				throw new RuntimeException('You are not assigned to this shipment as a driver.');
			}
			$shipment->update(['rated_by_driver' => true]);
			$ratee = $shipment->merchant?->user;
		} else {
			throw new RuntimeException('Only drivers and merchants can submit ratings.');
		}

		if (!$ratee) {
			throw new RuntimeException('The other party is not assigned to this shipment yet.');
		}

		return $ratee;
	}
}
