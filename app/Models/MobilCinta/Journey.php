<?php

namespace App\Models\MobilCinta;

use App\Models\Ruangan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Validator;

class Journey extends Model
{
    use HasFactory;

    public const STATUS_REQUESTED = 'diajukan';
    public const STATUS_ASSIGNED = 'ditugaskan';
    public const STATUS_DEPARTED = 'berangkat';
    public const STATUS_COMPLETED = 'selesai';
    public const STATUS_REJECTED = 'ditolak';
    public const STATUS_CANCELLED = 'dibatalkan';

    protected $table = 'mobil_cinta_perjalanan';

    // Status is intentionally excluded: workflow services own transitions.
    protected $fillable = [
        'nomor_perjalanan',
        'diajukan_oleh_id',
        'ruangan_id',
        'nama_pasien',
        'nomor_rekam_medis',
        'jenis_kelamin_pasien',
        'penjamin',
        'tujuan',
        'catatan',
        'diajukan_pada',
        'sopir_id',
        'nama_sopir',
        'kendaraan_id',
        'ditugaskan_oleh_id',
        'ditugaskan_pada',
        'kilometer_berangkat',
        'latitude_berangkat',
        'longitude_berangkat',
        'foto_berangkat',
        'waktu_berangkat',
        'kilometer_sampai',
        'latitude_sampai',
        'longitude_sampai',
        'foto_sampai',
        'waktu_sampai',
        'catatan_sopir',
        'alasan_penolakan',
    ];

    protected $attributes = ['status' => self::STATUS_REQUESTED];

    protected $casts = [
        'diajukan_pada' => 'datetime',
        'ditugaskan_pada' => 'datetime',
        'kilometer_berangkat' => 'decimal:2',
        'kilometer_sampai' => 'decimal:2',
        'latitude_berangkat' => 'decimal:7',
        'longitude_berangkat' => 'decimal:7',
        'latitude_sampai' => 'decimal:7',
        'longitude_sampai' => 'decimal:7',
        'waktu_berangkat' => 'datetime',
        'waktu_sampai' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (Journey $journey) {
            self::validateKilometers($journey->kilometer_berangkat, $journey->kilometer_sampai);
        });
    }

    public static function validateKilometers(mixed $startKm, mixed $endKm): void
    {
        $validator = Validator::make(
            ['kilometer_berangkat' => $startKm, 'kilometer_sampai' => $endKm],
            [
                'kilometer_berangkat' => ['nullable', 'numeric', 'min:0'],
                'kilometer_sampai' => ['nullable', 'numeric', 'min:0'],
            ]
        );

        $validator->after(function ($validator) use ($startKm, $endKm) {
            if ($startKm !== null && $endKm !== null && is_numeric($startKm) && is_numeric($endKm) && (float) $endKm < (float) $startKm) {
                $validator->errors()->add('kilometer_sampai', 'Kilometer sampai tidak boleh lebih kecil dari kilometer berangkat.');
            }
        });

        $validator->validate();
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'diajukan_oleh_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'sopir_id');
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'kendaraan_id');
    }

    public function coordinator(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'ditugaskan_oleh_id');
    }
}
