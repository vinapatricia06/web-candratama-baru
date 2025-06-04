<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Maintenance;
use App\Models\Omset;
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
        $metode_pembayaran = $request->metode_pembayaran;
        $data_project = ProgressProject::with('klien')->findOrFail($project_id);
        $kode_transaksi = 'ORDER-' . rand();

        DB::beginTransaction();

        try {
            if ($metode_pembayaran == "midtrans") {
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
                    'metode' => $metode_pembayaran,
                    'status' => 'success',
                    'project_id' => $kode_transaksi,
                    'sumber_lead' => $sumber_lead,
                    'snap_token' => $snapToken,
                ]);
            } else {
                $data_project->kode_transaksi = $kode_transaksi;
                $data_project->status_pembayaran = 'Sudah Dibayar';
                $data_project->save();

                DB::commit();
                return response()->json([
                    'metode' => $metode_pembayaran,
                    'status' => 'success',
                    'project_id' => $kode_transaksi,
                    'sumber_lead' => $sumber_lead,
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Transaksi gagal: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createTransactionMaintenance(Request $request)
    {
        $maintenance_id = $request->maintenance_id;
        $sumber_lead = $request->sumber_lead;
        $metode_pembayaran = $request->metode_pembayaran;
        $data_maintenance = Maintenance::with('project.klien')->findOrFail($maintenance_id);
        $kode_transaksi = 'MAINTENANCE-' . rand();

        DB::beginTransaction();

        try {
            if ($metode_pembayaran == "midtrans") {
                // insert data ke MIDTRANS
                $transactionDetails = [
                    'order_id' => $kode_transaksi,
                    'gross_amount' => $data_maintenance->biaya_tambahan,
                ];

                $customerDetails = [
                    'first_name' => $data_maintenance->project->klien->nama_klien,
                    'last_name' => '',
                    'email' => $data_maintenance->project->klien->no_induk . '@gmail.com',
                    'phone' => $data_maintenance->project->klien->no_hp,
                ];

                $transaction = [
                    'transaction_details' => $transactionDetails,
                    'customer_details' => $customerDetails,
                ];
                $snapToken = Snap::getSnapToken($transaction);

                $data_maintenance->kode_transaksi = $kode_transaksi;
                $data_maintenance->save();

                DB::commit();
                return response()->json([
                    'metode' => $metode_pembayaran,
                    'status' => 'success',
                    'project_id' => $kode_transaksi,
                    'sumber_lead' => $sumber_lead,
                    'snap_token' => $snapToken,
                ]);
            } else {
                $data_maintenance->kode_transaksi = $kode_transaksi;
                $data_maintenance->status_pembayaran = 'Sudah Dibayar';
                $data_maintenance->save();

                DB::commit();
                return response()->json([
                    'metode' => $metode_pembayaran,
                    'status' => 'success',
                    'project_id' => $kode_transaksi,
                    'sumber_lead' => $sumber_lead,
                ]);
            }
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
        $data = ProgressProject::where('kode_transaksi', $request->order_id)->first();
        if ($data == null) {
            $data = Maintenance::where('kode_transaksi', $request->order_id)->first();
        }

        switch ($request->transaction_status) {
            case 'capture':
            case 'settlement':
                $data->status_pembayaran = 'Sudah Dibayar';
                break;
            case 'pending':
                $data->status_pembayaran = 'Menunggu Pembayaran';
                break;
            case 'deny':
                $data->status_pembayaran = 'Pembayaran Ditolak';
                break;
            case 'expire':
                $data->status_pembayaran = 'Kadaluarsa';
                break;
            case 'cancel':
                $data->status_pembayaran = 'Dibatalkan';
                break;
            default:
                $data->status_pembayaran = 'Status Tidak Dikenal';
                break;
        }

        $data->save();

        return response()->json([
            'message' => 'Callback processed successfully'
        ], 200);
    }

    public function sukses($id, $sumber, $metode)
    {
        $data = ProgressProject::with('klien')->where('kode_transaksi', $id)->first();
        Omset::create([
            'progress_projects_id' => $data->id,
            'tanggal' => date('Y-m-d'),
            'sumber_lead' => $sumber,
            'nominal' => $data->nominal,
            'metode_pembayaran' => $metode,
            'catatan_pembayaran' => 'PROJECT'
        ]);
        return view('response.sukses', compact('data'));
    }

    public function gagal($id, $sumber)
    {
        $data = ProgressProject::with('klien')->where('kode_transaksi', $id)->first();
        return view('response.gagal', compact('data'));
    }

    public function suksesMaintenance($id, $sumber, $metode)
    {
        $data = Maintenance::with('project.klien')->where('kode_transaksi', $id)->first();
        Omset::create([
            'progress_projects_id' => $data->progress_projects_id,
            'tanggal' => date('Y-m-d'),
            'sumber_lead' => $sumber,
            'nominal' => $data->biaya_tambahan,
            'metode_pembayaran' => $metode,
            'catatan_pembayaran' => 'MAINTENANCE'
        ]);
        return view('response.sukses_maintenance', compact('data'));
    }

    public function gagalMaintenance($id, $sumber)
    {
        $data = Maintenance::with('project.klien')->where('kode_transaksi', $id)->first();
        return view('response.gagal_maintenance', compact('data'));
    }
}
