<?php

namespace App\Http\Controllers\API;

use App\Models\WorkOrder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;

class WorkOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = WorkOrder::with('assignedOperator');

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date
        if ($request->has('date')) {
            $query->whereDate('created_at', $request->date);
        }

        return $this->sendResponse($query->get(), 'Berhasil menampilkan data work order');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_name' => 'required',
            'quantity' => 'required|integer|min:1',
            'deadline' => 'required|date',
            'assigned_operator_id' => 'required|exists:users,id'
        ]);

        $workOrder = WorkOrder::create($validated + ['status' => 'Pending']);

        return $this->sendResponse($workOrder, 'Work order berhasil dibuat');
    }

    public function show($id)
    {
        // Find the work order by ID and load the associated assigned operator.
        $workOrder = WorkOrder::with('assignedOperator')->find($id);

        // Check if the work order exists
        if (!$workOrder) {
            return $this->sendError('Work order not found', 404);
        }

        // Return the work order data in the response
        return $this->sendResponse($workOrder, 'Work order details retrieved successfully');
    }


    public function update(Request $request, WorkOrder $workOrder)
    {
        $validated = $request->validate([
            'status' => 'sometimes|in:Pending,In Progress,Completed,Canceled',
            'assigned_operator_id' => 'sometimes|exists:users,id'
        ]);

        $workOrder->update($validated);

        return $this->sendResponse($workOrder, 'Work order berhasil diperbarui');
    }

    public function destroy($id)
    {
        $workOrder = WorkOrder::find($id);

        if (!$workOrder) {
            return $this->sendError('Work order not found', 404);
        }

        $workOrder->statusUpdates()->delete();

        $workOrder->delete();

        return $this->sendResponse(null, 'Work order berhasil dihapus');
    }


    public function generateReport(Request $request)
    {
        // Ambil data WorkOrder berdasarkan parameter status dan tanggal
        $query = WorkOrder::with('assignedOperator');

        // Filter berdasarkan status jika ada
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan tanggal jika ada
        if ($request->has('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Ambil data yang sesuai dengan query
        $workOrders = $query->get();

        // Format data laporan
        $report = $workOrders->map(function ($workOrder) {
            return [
                'wo_number' => $workOrder->wo_number,
                'product_name' => $workOrder->product_name,
                'quantity' => $workOrder->quantity,
                'status' => $workOrder->status,
                'assigned_operator' => [
                    'id' => $workOrder->assignedOperator->id,
                    'name' => $workOrder->assignedOperator->name,
                    'email' => $workOrder->assignedOperator->email,
                ],
                'created_at' => $workOrder->created_at,
                'updated_at' => $workOrder->updated_at,
            ];
        });

        return $this->sendResponse($report, 'Laporan berhasil dihasilkan');
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
