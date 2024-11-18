<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CriteriaController extends Controller
{
    /**
     * Get the user's current criteria.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCriteria()
    {
        try {
            $user = Auth::user();

            // Validate that user has a package
            if (!$user->purchased_package) {
                return response()->json([
                    'success' => false,
                    'message' => 'You need to purchase a package before accessing criteria.'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'criteria' => json_decode($user->criteria ?? '[]'),
                    'criteria_limit' => $user->criteria_limit
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting user criteria', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving criteria.'
            ], 500);
        }
    }

    /**
     * Update the user's match criteria.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCriteria(Request $request)
    {
        try {
            $user = Auth::user();

            // Validate that user has a package
            if (!$user->purchased_package) {
                return response()->json([
                    'success' => false,
                    'message' => 'You need to purchase a package before setting criteria.'
                ], 403);
            }

            // Get and validate criteria
            $criteria = $request->input('criteria');
            
            // Validate criteria count against package limit
            $criteriaCount = count((array)$criteria);
            if ($criteriaCount > $user->criteria_limit) {
                return response()->json([
                    'success' => false,
                    'message' => "You can only select up to {$user->criteria_limit} criteria with your current package."
                ], 422);
            }

            // Validate individual criteria
            $this->validateCriteria($criteria);

            // Update user criteria
            $user->criteria = json_encode($criteria);
            $user->save();

            Log::info('User criteria updated', [
                'user_id' => $user->id,
                'criteria_count' => $criteriaCount
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Criteria updated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating user criteria', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating criteria.'
            ], 500);
        }
    }

    /**
     * Validate the criteria data.
     *
     * @param  array  $criteria
     * @return void
     * @throws \InvalidArgumentException
     */
    private function validateCriteria($criteria)
    {
        foreach ($criteria as $key => $value) {
            switch ($key) {
                case 'age':
                    if (!isset($value['min']) || !isset($value['max']) ||
                        $value['min'] < 18 || $value['max'] > 100 ||
                        $value['min'] > $value['max']) {
                        throw new \InvalidArgumentException('Invalid age range');
                    }
                    break;

                case 'ethnicity':
                    if (!is_array($value) || count($value) > 4) {
                        throw new \InvalidArgumentException('Invalid ethnicity selection');
                    }
                    break;

                case 'children':
                    if (!in_array($value, ['yes', 'no', 'doesnt_matter'])) {
                        throw new \InvalidArgumentException('Invalid children preference');
                    }
                    break;

                case 'wantsKids':
                    if (!in_array($value, ['yes', 'no', 'maybe'])) {
                        throw new \InvalidArgumentException('Invalid wants kids preference');
                    }
                    break;

                case 'bodyType':
                    $validTypes = [
                        'Slim', 'Athletic', 'Average', 'Curvy', 'Muscular',
                        'A few extra pounds', 'Big and Beautiful'
                    ];
                    if (!in_array($value, $validTypes)) {
                        throw new \InvalidArgumentException('Invalid body type');
                    }
                    break;

                case 'height':
                    if (!isset($value['min']) || !isset($value['max']) ||
                        $value['min'] < 60 || $value['max'] > 96 ||
                        $value['min'] > $value['max']) {
                        throw new \InvalidArgumentException('Invalid height range');
                    }
                    break;

                case 'religion':
                    if (!is_array($value) || count($value) > 4) {
                        throw new \InvalidArgumentException('Invalid religion selection');
                    }
                    break;

                case 'relationshipStatus':
                    $validStatuses = ['Never married', 'Divorced', 'Separated'];
                    if (!in_array($value, $validStatuses)) {
                        throw new \InvalidArgumentException('Invalid relationship status');
                    }
                    break;

                case 'location':
                    if (!isset($value['country']) || !isset($value['state']) || !isset($value['city'])) {
                        throw new \InvalidArgumentException('Invalid location data');
                    }
                    break;

                case 'income':
                    if (!isset($value['min']) || !isset($value['max']) ||
                        $value['min'] > $value['max']) {
                        throw new \InvalidArgumentException('Invalid income range');
                    }
                    break;

                default:
                    throw new \InvalidArgumentException("Unknown criteria: {$key}");
            }
        }
    }
}
