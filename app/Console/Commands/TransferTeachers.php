<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Circle;
use App\School;
use App\Zone;
use App\Teacher;
use Illuminate\Support\Facades\DB;

class TransferTeachers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'teacher:transfer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transfer teachers according to rationalisation and some given priority parameters';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        DB::update('UPDATE `teachers` SET `deleted_at`=NOW(),`old_school_id`=null');
        DB::update('UPDATE `teachers` join data_teachers on data_teachers.id = teachers.id
            set teachers.school_id = data_teachers.school_id');
        $schools = new School;
        $allSchools = $schools->where('teachers', '>', 0)->get();
        /** Restore teachers for circle wise transfer */
        $circles = Circle::all();
        foreach ($circles as $circle) {
            $this->info('Circle:[' . $circle->id . '] ' . $circle->circle_name);
            /** Lift of Circles having surplus teachers */
            $fromSchools = $circle->schools()
                ->where('movement', '<', 0)
                ->get();
            /** Iterate over all schools to restore surplus teachers for transfer  */
            foreach ($fromSchools as $fromSchool) {
                $fromSchool->teachers()
                    ->withTrashed()
                    ->where('gender', 'MALE')
                    ->orderBy('joined_on', 'desc')
                    ->take($fromSchool->movement*-1)
                    ->restore();
            }
            $this->info('Restored: ' . $circle->schools()
                ->where('movement', '<', 0)
                ->get()->count());
        }
        exit();
        /** Circle wise transfer */
        foreach ($circles as $circle) {
            /** List of all surplus teachers sorted by least residency period in the circle */
            $teachers = $circle->teachers()->orderBy('joined_on')->get();
            /** List of all schools with shortage of teachers */
            $toSchools = $circle->schools()
                ->where('movement', '>', 0)
                ->get();
            $this->info('Transferring in Circle:[' . $circle->id . '] ' . $circle->circle_name);
            $this->transfer($teachers, $toSchools);
        }
        /** Transfer outside circle within zone */
        $zones = Zone::all();
        foreach ($zones as $zone) {
            /** List of Schools in the zone */
            $this->info('Transferring in Zone:[' . $zone->id . '] ' . $zone->zone_name);
            $fromSchools = $zone->schools()->where('movement', '<', 0)->get();
            $teachers = collect();
            foreach ($fromSchools as $fromSchool) {
                $fromTeachers = $fromSchool->teachers()->get();
                foreach ($fromTeachers as $teacher) {
                    $teachers->push($teacher);
                }
            }
            $toSchools = $zone->schools()->where('movement', '>', 0)->get();
            $this->transfer($teachers, $toSchools);
        }
        /** Transfer outside zone */
        $this->info('Transferring in the universe');
        /** List of Schools in the database */
        $fromSchools = School::where('movement', '<', 0)->get();
        $teachers = collect();
        foreach ($fromSchools as $fromSchool) {
            $fromTeachers = $fromSchool->teachers()->get();
            foreach ($fromTeachers as $teacher) {
                $teachers->push($teacher);
            }
        }
        $toSchools = School::where('movement', '>', 0)->get();
        $this->transfer($teachers, $toSchools);
    }

    public function transfer($teachers, $toSchools)
    {
        /** Iterate over and fill with teachers */
        $teachers->sortByDesc('gender');
        $teachers->sortBy('joined_on');
        $this->info('Teachers Available:' . $teachers->count()
            . ' Required:' . $toSchools->sum('movement') . ' Line:' . __LINE__);
        $transferred = 0;
        foreach ($toSchools as $school) {
            $movements = $school->movement;
            for ($i = 0; $i < $movements; $i++) {
                if ($teachers->count()) {
                    $teacher = $teachers->shift();
                    $teacher->old_school_id = $teacher->school_id;
                    $oldSchool = School::find($teacher->old_school_id);
                    $teacher->school()->associate($school);
                    $teacher->save();
                    $school->movement--;
                    $school->save();
                    $oldSchool->movement++;
                    $oldSchool->save();
                    $teacher->delete();
                    $transferred++;
                }
            }
        }
        $this->error('Transfered:' . $transferred);
    }
}
