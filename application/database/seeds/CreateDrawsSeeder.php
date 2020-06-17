<?php

use App\Draw;
use App\Event;
use Illuminate\Database\Seeder;

class CreateDrawsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [ 
                'description' => 'Sorteo 1',
                'status' => 1,
                'event_type' => 'Evento Tipo Sorteo',
            ],
            [ 
                'description' => 'Sorteo 2',
                'status' => 1,
                'event_type' => 'Evento Tipo Sorteo',
            ],
            [ 
                'description' => 'Sorteo 3',
                'status' => 1,
                'event_type' => 'Evento Tipo Sorteo',
            ],
        ];
        foreach ($data as $element) {
            $event = Event::where('description',$element['event_type'])->first();
            Draw::create([
                'description' => $element['description'],
                'status' => $element['status'],
                'event_id' => $event->id,
            ]);
        }
    }
}
