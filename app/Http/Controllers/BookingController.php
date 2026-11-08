<?php
namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Background;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    public function getBookedTimes($date)
    {
        $packageId = request()->query('package_id');

        $booked = Booking::whereDate('date', $date)
            ->when($packageId, fn ($q) => $q->where('package_id', $packageId))
            ->pluck('time')
            ->toArray();

        return response()->json([
            'booked_times' => $booked,
        ]);
    }

    public function showCalendar()
    {
        return view('background');
    }
    public function home()
    {
        try {
            \App\Support\Site::ensureSeeded();
        } catch (\Throwable $e) {
        }

        $promos = collect();
        try {
            $promos = \Illuminate\Support\Facades\Cache::remember(
                'site_promos_running',
                180,
                fn () => \App\Models\SitePromo::running()->get()
            );
        } catch (\Throwable $e) {
            $promos = collect();
        }

        $initialPackages = collect();
        try {
            $rawPackages = \App\Models\Package::with(['media', 'backgrounds.media'])->orderBy('id')->get();
            if ($rawPackages->isEmpty()) {
                $rawPackages = \App\Support\Site::bookingPackages();
            }
            $initialPackages = PackageController::formatPackages($rawPackages);
        } catch (\Throwable $e) {
            try {
                $initialPackages = PackageController::formatPackages(\App\Support\Site::bookingPackages());
            } catch (\Throwable $e2) {
                $initialPackages = collect();
            }
        }

        $waNumber = '6285117607254';
        try {
            $waNumber = preg_replace('/\D+/', '', (string) \App\Support\Site::get('wa_number', '6285117607254')) ?: '6285117607254';
        } catch (\Throwable $e) {
        }

        return view('home', [
            'promos' => $promos,
            'initialPackages' => $initialPackages,
            'waNumber' => $waNumber,
        ]);
    }

    public function submitBooking(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'whatsapp' => 'required|string|max:20',
            'people_count' => 'required|integer|min:1',
            'package_id' => 'required|exists:packages,id',
            'background_id' => 'required|exists:backgrounds,id',
            'date' => 'required|date',
            'time' => 'required|string',
            'price' => 'required|integer|min:0',
            'voucher_id' => 'nullable|exists:vouchers,id',
            'confirmation' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
              \Log::error('Validasi booking gagal', $validator->errors()->toArray());
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Validasi voucher kalau ada
            if ($request->voucher_id) {
               $voucher = \App\Models\Voucher::find($request->voucher_id);

            if (!$voucher) {
                return response()->json(['message' => 'Voucher tidak ditemukan.'], 400);
            }

            if (!$voucher->is_active) {
                return response()->json(['message' => 'Voucher tidak aktif.'], 400);
            }

            if ($voucher->start_date && $voucher->start_date > now()) {
                return response()->json(['message' => 'Voucher belum berlaku.'], 400);
            }

            if ($voucher->end_date && $voucher->end_date < now()->toDateString()) {
                return response()->json(['message' => 'Voucher sudah kedaluwarsa.'], 400);
            }

            if ($voucher->usage_limit && $voucher->used_count >= $voucher->usage_limit) {
                return response()->json(['message' => 'Voucher sudah mencapai batas pemakaian.'], 400);
            }

                $package = \App\Models\Package::find($request->package_id);
                $basePrice = $package->price;
                $extraPricePerPerson = (int) $package->extras_people;
                // Harga tambahan per orang
                $includedPeople = !empty($package->included_people)
                    ? (int) $package->included_people
                    : match ($package->id) {
                        2 => 2, // package 2 include 2 orang
                        default => 1 // package 1 & 3 include 1 orang
                    };
                // Hitung total base price berdasarkan jumlah orang
                $totalPrice = $basePrice;
                if ($request->people_count > $includedPeople) {
                    $totalPrice += ($request->people_count - $includedPeople) * $extraPricePerPerson;
                }

                // Hitung diskon
                $discountAmount = intval(round($totalPrice * ($voucher->discount_percent / 100)));
                $finalPrice = max(0, $totalPrice - $discountAmount);

                // Tambahkan log agar bisa dicek
                \Log::info('Perhitungan Booking:', [
                    'package_id' => $package->id,
                    'base_price' => $basePrice,
                    'people_count' => $request->people_count,
                    'extra_price_per_person' => $extraPricePerPerson,
                    'total_price' => $totalPrice,
                    'discount_percent' => $voucher->discount_percent,
                    'discount_amount' => $discountAmount,
                    'final_price' => $finalPrice,
                    'request_price' => $request->price,
                ]);

                // Validasi harga
                if (abs((int) $request->price - (int) $finalPrice) > 1) {
                    return response()->json([
                        'message' => 'Harga tidak valid. Cek kembali perhitungan.',
                    ], 400);
                }

            }

            // Simpan booking
            $booking = Booking::create([
                'name' => $request->name,
                'email' => $request->email,
                'whatsapp' => $request->whatsapp,
                'people_count' => $request->people_count,
                'package_id' => $request->package_id,
                'background_id' => $request->background_id,
                'date' => $request->date,
                'time' => $request->time,
                'price' => $request->price,
                'voucher_id' => $request->voucher_id,
                'confirmation' => $request->confirmation ?? 0,
            ]);

            // Tambah penggunaan voucher
            if ($request->voucher_id) {
                $voucher->increment('used_count');
            }

            return response()->json([
                'message' => 'Booking berhasil disimpan',
                'booking' => $booking
            ]);
        } catch (\Exception $e) {
             \Log::error('Gagal menyimpan booking', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'request' => $request->all(),
                ]);
            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan booking',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function getBackgrounds(Request $request)
    {
        try {
            $packageId = $request->query('package_id');

            if (!$packageId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parameter package_id wajib diisi.'
                ], 400);
            }

            $package = \App\Models\Package::with('backgrounds')->find($packageId);

            if (!$package) {
                return response()->json([
                    'success' => false,
                    'message' => 'Paket tidak ditemukan.'
                ], 404);
            }

            $fallbacks = [
                asset('img/group(3).png'),
                asset('img/group(4).png'),
                asset('img/group(2).png'),
                asset('img/group(1).png'),
            ];

            $backgrounds = $package->backgrounds->values()->map(function ($bg, $idx) use ($fallbacks) {
                $url = $bg->getFirstMediaUrl('image');
                return [
                    'id' => $bg->id,
                    'name' => $bg->name,
                    'image_url' => $url ?: $fallbacks[$idx % count($fallbacks)],
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $backgrounds
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data background.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


}
