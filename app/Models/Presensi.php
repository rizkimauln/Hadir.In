<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Presensi
 * 
 * @property int $id
 * @property string $nik
 * @property Carbon $tgl_presensi
 * @property Carbon $jam_in
 * @property Carbon|null $jam_out
 * @property string $foto_in
 * @property string|null $foto_out
 * @property string $lokasi_in
 * @property string|null $lokasi_out
 * 
 * @property Karyawan $karyawan
 *
 * @package App\Models
 */
class Presensi extends Model
{
	protected $table = 'presensi';
	public $timestamps = false;

	protected $casts = [
		'tgl_presensi' => 'datetime',
		'jam_in' => 'datetime',
		'jam_out' => 'datetime'
	];

	protected $fillable = [
		'nik',
		'tgl_presensi',
		'jam_in',
		'jam_out',
		'foto_in',
		'foto_out',
		'lokasi_in',
		'lokasi_out'
	];

	public function karyawan()
	{
		return $this->belongsTo(Karyawan::class, 'nik');
	}
}
