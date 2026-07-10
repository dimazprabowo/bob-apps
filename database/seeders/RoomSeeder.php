<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Enums\RoomStatus;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = [
            [
                'name' => 'Ruang Rapat Utama',
                'location' => 'Lantai 1',
                'capacity' => 20,
                'facilities' => ['Proyektor', 'Whiteboard', 'AC', 'Sound System'],
                'status' => RoomStatus::Available->value,
                'description' => 'Ruang rapat utama dengan kapasitas besar',
            ],
            [
                'name' => 'Ruang Meeting 1',
                'location' => 'Lantai 2',
                'capacity' => 8,
                'facilities' => ['TV LED', 'AC', 'Whiteboard'],
                'status' => RoomStatus::Available->value,
                'description' => 'Ruang meeting kecil untuk diskusi tim',
            ],
            [
                'name' => 'Ruang Meeting 2',
                'location' => 'Lantai 2',
                'capacity' => 6,
                'facilities' => ['TV LED', 'AC'],
                'status' => RoomStatus::Available->value,
                'description' => 'Ruang meeting kecil',
            ],
            [
                'name' => 'Ruang Konferensi',
                'location' => 'Lantai 3',
                'capacity' => 40,
                'facilities' => ['Proyektor', 'Microphone', 'AC', 'Sound System', 'Video Conference'],
                'status' => RoomStatus::Available->value,
                'description' => 'Ruang konferensi besar dengan fasilitas video conference',
            ],
            [
                'name' => 'Ruang Brainstorming',
                'location' => 'Lantai 1',
                'capacity' => 12,
                'facilities' => ['Whiteboard', 'AC', 'Sticky Board'],
                'status' => RoomStatus::Maintenance->value,
                'description' => 'Ruang untuk sesi brainstorming — sedang maintenance',
            ],
        ];

        foreach ($rooms as $room) {
            Room::firstOrCreate(
                ['name' => $room['name']],
                collect($room)->except('name')->toArray()
            );
        }
    }
}
