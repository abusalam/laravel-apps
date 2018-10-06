<?php

/**
 * Version 1.0.1
 * Laravel Command for Rationalisation of Primary Teachers
 *
 * @author  Abu Salam Parvez Alam <abusalamparvezalam@gmail.com>
 * @license MIT
 *
 */
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\School;

/**
 * Undocumented class
 */
class RationalizeSchoolTeachers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'teacher:rationalize
                    {minStudents=30 : Number of minimum students in a school below this no extra teachers required}
                    {maxTeachers=14 : above this no adjustment required for posting extra teachers}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate the required number of teachers and adjustments needed based on overall ratio';

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
        $minStudents = $this->argument('minStudents');
        $maxTeachers = $this->argument('maxTeachers');
        $schools = new School;
        $allSchools = $schools->where('teachers', '>', 0)->orderBy('students', 'desc')->get();
        $targetRatio = $schools->getTargetRatio();
        $allSchools->each(function ($school) use ($targetRatio, $minStudents, $maxTeachers) {
            $school->movement=0;
            while (($school->final() < 8) && ($school->ratio() > $targetRatio)) {
                $school->movement++;
            }
            while (($school->final() > 2) && ($school->ratio() < $targetRatio)) {
                $school->movement--;
            }
            for ($i=1; $i<$maxTeachers; $i++) {
                if ($school->final()<=$i) {
                    if ($school->students>floor($i*$minStudents)) {
                        $school->movement++;
                    }
                }
            }
            $school->final=$school->final();
            $school->save();
        });
        $this->info('Target Ratio: ' . $targetRatio);
        $this->info('Enrollments:'.$allSchools->sum('students')
            .' Teachers:'.$allSchools->sum('teachers')
            .' Alloted:'.$allSchools->sum('final')
            .' Surplus: ('.($allSchools->sum('movement')*-1).')');
    }
}
