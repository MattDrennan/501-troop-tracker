<?php

declare(strict_types=1);

namespace Database\Seeders\Conversions;

use App\Models\EventUpload;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventUploadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $legacy_uploads = DB::table('uploads')
            ->join('tt_troopers', 'uploads.trooperid', '=', 'tt_troopers.id')
            ->join('tt_events', 'uploads.troopid', '=', 'tt_events.id')
            ->select('uploads.*')
            ->get();

        foreach ($legacy_uploads as $upload)
        {
            $e = EventUpload::find($upload->id) ?? new EventUpload(['id' => $upload->id]);

            $e->event_id = $upload->troopid;
            $e->trooper_id = $upload->trooperid;
            $e->filename = $upload->filename;

            $e->save();
        }
    }
}