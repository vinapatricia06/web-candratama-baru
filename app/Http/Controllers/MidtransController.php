<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ProgressProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Prompts\Progress;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransController extends Controller
{
    public function __construct()
    {
        // Konfigurasi Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }
    public function createTransaction(Request $request)
    {
        $project_id = $request->project_id;
        $sumber_lead = $request->sumber_lead;
        $data_project = ProgressProject::with('klien')->findOrFail($project_id);
        $kode_transaksi = 'ORDER-' . rand();

        DB::beginTransaction();

        try {
            // insert data ke MIDTRANS
            $transactionDetails = [
                'order_id' => $kode_transaksi,
                'gross_amount' => $data_project->nominal,
            ];

            $customerDetails = [
                'first_name' => $data_project->klien->nama_klien,
                'last_name' => '',
                'email' => $data_project->klien->no_induk . '@gmail.com',
                'phone' => $data_project->klien->no_hp,
            ];

            $transaction = [
                'transaction_details' => $transactionDetails,
                'customer_details' => $customerDetails,
            ];
            $snapToken = Snap::getSnapToken($transaction);

            $data_project->kode_transaksi = $kode_transaksi;
            $data_project->snap_token = $snapToken;
            $data_project->save();

            DB::commit();
            return response()->json([
                'status' => 'success',
                'project_id' => $project_id,
                'sumber_lead' => $sumber_lead,
                'snap_token' => $snapToken,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Transaksi gagal: ' . $e->getMessage()
            ], 500);
        }
    }

    public function callback(Request $request)
    {
        $serverKey = config('midtrans.server_key');
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed !== $request->signature_key) {
            return response()->json([
                'message' => 'Invalid signature key',
                'expected' => $hashed,
                'received' => $request->signature_key
            ], 403);
        }
        $progressProject = ProgressProject::where('kode_transaksi', $request->order_id)->first();

        switch ($request->transaction_status) {
            case 'capture':
            case 'settlement':
                $progressProject->status_pembayaran = 'Sudah Dibayar';
                break;
            case 'pending':
                $progressProject->status_pembayaran = 'Menunggu Pembayaran';
                break;
            case 'deny':
                $progressProject->status_pembayaran = 'Pembayaran Ditolak';
                break;
            case 'expire':
                $progressProject->status_pembayaran = 'Kadaluarsa';
                break;
            case 'cancel':
                $progressProject->status_pembayaran = 'Dibatalkan';
                break;
            default:
                $progressProject->status_pembayaran = 'Status Tidak Dikenal';
                break;
        }

        $progressProject->save();

        return response()->json([
            'message' => 'Callback processed successfully'
        ], 200);
    }
}
