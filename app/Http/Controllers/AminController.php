<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AminController extends Controller
{
    /**
     * Toggle amin status untuk doa
     */
    public function toggleAmin(Request $request): JsonResponse
    {
        $request->validate([
            'donation_id' => 'required|exists:donations,id',
        ]);

        $donation = Donation::findOrFail($request->donation_id);
        
        // Untuk sementara gunakan session ID sebagai identifier user
        // Dalam implementasi nyata, gunakan auth()->id() jika user sudah login
        $userId = session()->getId();
        
        if ($donation->hasUserAmin($userId)) {
            // User sudah amin, hapus amin
            $donation->removeAmin($userId);
            $action = 'removed';
        } else {
            // User belum amin, tambah amin
            $donation->addAmin($userId);
            $action = 'added';
        }

        return response()->json([
            'success' => true,
            'action' => $action,
            'amin_count' => $donation->amin_count,
            'has_amin' => $donation->hasUserAmin($userId),
        ]);
    }

    /**
     * Get amin status untuk doa
     */
    public function getAminStatus(Request $request): JsonResponse
    {
        $request->validate([
            'donation_id' => 'required|exists:donations,id',
        ]);

        $donation = Donation::findOrFail($request->donation_id);
        $userId = session()->getId();
        
        return response()->json([
            'success' => true,
            'amin_count' => $donation->amin_count,
            'has_amin' => $donation->hasUserAmin($userId),
        ]);
    }
}
