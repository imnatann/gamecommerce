<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DisputeStatus;
use App\Http\Controllers\Controller;
use App\Models\Dispute;
use Illuminate\Http\Request;

class DisputeController extends Controller
{
    public function index(Request $request)
    {
        $query = Dispute::with('order', 'buyer', 'seller');

        if ($request->filled('status')) {
            $query->where('status', DisputeStatus::from($request->status));
        }

        $disputes = $query->latest()->paginate(20);

        $statusCounts = collect(DisputeStatus::cases())->mapWithKeys(function ($status) {
            return [$status->value => Dispute::where('status', $status)->count()];
        });

        $openCount = Dispute::where('status', DisputeStatus::OPEN)->count();
        $underReviewCount = Dispute::where('status', DisputeStatus::UNDER_REVIEW)->count();
        $resolvedCount = Dispute::whereIn('status', [DisputeStatus::RESOLVED_BUYER, DisputeStatus::RESOLVED_SELLER])->count();

        return view('admin.disputes', compact(
            'disputes',
            'statusCounts',
            'openCount',
            'underReviewCount',
            'resolvedCount'
        ));
    }

    public function show(Dispute $dispute)
    {
        $dispute->load('order.items.product', 'buyer', 'seller', 'messages');

        return view('admin.disputes', compact('dispute'));
    }

    public function resolve(Request $request, Dispute $dispute)
    {
        $request->validate([
            'resolution' => 'required|string|in:refund,release,partial',
            'partial_amount' => 'nullable|integer|min:0',
            'note' => 'required|string|max:500',
        ]);

        $status = match ($request->resolution) {
            'refund' => DisputeStatus::RESOLVED_BUYER,
            'release' => DisputeStatus::RESOLVED_SELLER,
            'partial' => DisputeStatus::RESOLVED_BUYER,
        };

        $dispute->update([
            'status' => $status,
            'resolution' => $request->resolution,
            'resolved_by' => auth()->id(),
        ]);

        $dispute->messages()->create([
            'user_id' => auth()->id(),
            'message' => $request->note,
            'is_admin' => true,
        ]);

        return back()->with('success', 'Dispute berhasil diselesaikan');
    }
}