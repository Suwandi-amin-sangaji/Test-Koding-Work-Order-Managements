<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use App\Models\StatusUpdates;
use App\Http\Controllers\Controller;

class OperatorController extends Controller
{
    public function index(Request $request)
    {
        $operatorId = auth()->user()->id;
        
        $query = WorkOrder::with('assignedOperator');
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date')) {
            $query->whereDate('created_at', $request->date);
        }
        
        $query->where('assigned_operator_id', $operatorId);
    
        return $this->sendResponse($query->get(), 'Berhasil menampilkan data work order');
    }
    

    

    public function updateStatus(Request $request, WorkOrder $workOrder)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Validasi data dari request
        $validated = $request->validate([
            'new_status' => 'required|string',
            'quantity_updated' => 'required|integer|min:1',
            'notes' => 'nullable|string'
        ]);

        // Aturan perubahan status yang diperbolehkan
        $allowedTransitions = [
            'Pending' => 'In Progress',
            'In Progress' => 'Completed'
        ];

        // Pastikan status perubahan valid
        if (!isset($allowedTransitions[$workOrder->status]) || $allowedTransitions[$workOrder->status] !== $validated['new_status']) {
            return $this->sendError('Perubahan status tidak valid', 400);
        }

        // Pastikan jumlah yang diperbarui tidak lebih besar dari stok yang ada
        if ($validated['quantity_updated'] > $workOrder->quantity) {
            return $this->sendError('Jumlah yang diperbarui melebihi stok yang ada', 400);
        }

        // Simpan perubahan ke tabel status_updates
        StatusUpdates::create([
            'work_order_id' => $workOrder->id,
            'old_status' => $workOrder->status,
            'new_status' => $validated['new_status'],
            'quantity_updated' => $validated['quantity_updated'],
            'notes' => $validated['notes']
        ]);

        // Perbarui status dan kurangi quantity di tabel WorkOrder
        $workOrder->update([
            'status' => $validated['new_status'],
            'quantity' => $workOrder->quantity - $validated['quantity_updated']
        ]);

        return $this->sendResponse($workOrder, 'Berhasil memperbarui status work order');
    }



    public function details(WorkOrder $workOrder)
    {
        if ($workOrder->assigned_operator_id !== auth()->id()) {
            return $this->sendError('Unauthorized', 401);
        }

        return $this->sendResponse($workOrder, 'Berhasil menampilkan detail work order');
    }


    public function listOperators()
    {
        $operators = User::whereHas('role', function ($query) {
            $query->where('name', 'Operator'); // Sesuaikan field dengan database
        })->get();
    
        if ($operators->isEmpty()) {
            return $this->sendError('Tidak ada operator yang ditemukan', 404);
        }
    
        return $this->sendResponse($operators, 'Berhasil menampilkan data operator');
    }

    
}
