<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PengajuanIzin
 * 
 * @property int $id
 * @property string|null $nik
 * @property Carbon|null $tgl_izin
 * @property string|null $status
 * @property string|null $keterangan
 * @property string|null $status_pengajuan
 * 
 * @property Karyawan|null $karyawan
 *
 * @package App\Models
 */
class PengajuanIzin extends Model
{
	protected $table = 'pengajuan_izin';
	public $timestamps = false;

	protected $casts = [
		'tgl_izin' => 'datetime'
	];

	protected $fillable = [
		'nik',
		'tgl_izin',
		'status',
		'keterangan',
		'status_pengajuan'
	];

	public function karyawan()
	{
		return $this->belongsTo(Karyawan::class, 'nik');
	}
}
