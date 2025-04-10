<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Facial;
use App\Models\Lecture;
use App\Models\Student;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function PHPUnit\Framework\isEmpty;

class LectureController extends Controller
{ 
    /**
     * create a lecture
     */
    function create(Request $request){
        $request->validate([
            // 'course'=>['required'],
            'venue'=>['required'],
            'time'=>['required'],
            'lecturer'=>['required'],
            'unit'=>['required'],
            // 'faculty'=>['required'],
            // 'department'=>['required'],
            // 'unitCode'=>['required'],
            // 'unitName'=>['required'],
        ]);
        // check id the lecture table we have that unit 

        // if yes - upadte the count

        // if no - create the lecture
        // $lecturer_data = Unit::where('id',$request->unit)->where('lecturer',$request->lecturer)->first();
        // $lecturer_data = Lecture::where('unit',$request->unit)->where('lecturer',$request->lecturer)->first();
        $lecture = Lecture::create([
            'lecturer' => $request->lecturer,
            'venue' => $request->venue,
            // 'faculty' => $request->faculty,
            // 'department' => $request->department,
            // 'course' => $request->course,
            'time' => $request->time,
            'unit' => $request->unit,
            // 'unitCode' => $request->unitCode,
        ]);
        $data = Unit::where('id',$request->unit)->where('lecturer',$request->lecturer)->first();
        $lecture = Unit::where('id',$request->unit)->where('lecturer',$request->lecturer)->update(['count'=>$data->count + 1]);
       
        return response()->json($lecture);
    }

    /**
     * getting units
     */
    function units(Request $request){
        date_default_timezone_set('Africa/Nairobi');
        // logger()->info(date('Y-m-d H:i:s'));
        $units = Unit::where('faculty',$request->f)
                ->where('department',$request->d)
                ->where('course',$request->c)
                ->get()
                ->map(function($data){
                    return [
                        'id' => $data->id,
                        'name' => $data->name,
                        'code' => $data->code,
                        'count' => $data->count,
                        'lecturer' => $data->lecturer,
                    ];
                });
        $lecture = Lecture::where('lecturer',Auth::user()->staffNo)
                            ->get()
                            ->map(function($data){
                                return [
                                    'id' => $data->id,
                                    'lecturer' => $data->lecturer,
                                    'venue' => $data->venue,
                                    'time' => $data->time,
                                    'unit' => Unit::where('id',$data->unit)->get(),
                                ];
                            })
                            ;
        return response()->json([
            'units' => $units,
            'lectures' => $lecture
        ]);
    }



    /**
     * getting students for a lecture
     */
    function start(Request $request){
        $students = Student::where('faculty',$request->faculty)
                            ->where('department',$request->department)
                            ->where('course',$request->course)
                            ->whereJsonContains('units',$request->unitCode)
                            ->get()
                            ->map(function($student) use($request){
                                // logger()->info($student);
                                return [
                                    'id'=> $student->id,
                                    'name'=> $student->name,
                                    'regNo'=> $student->regNo,
                                    'faculty'=> $student->faculty,
                                    'department'=> $student->department,
                                    'course'=> $student->course,
                                    'units'=> $student->units,
                                    'present'=> Attendance::where('lecture',$request->lecture)->where('student',$student->regNo)->first() == null ? false : true,
                                    'clockIn'=> Attendance::where('lecture',$request->lecture)->where('student',$student->regNo)->first() == null 
                                                ?'-- --' 
                                                : Attendance::where('lecture', $request->lecture)->where('student', $student->regNo)->first()->clockIn,
                                    'facials' => Facial::where('student',$student->regNo)->first()
                                ];
                            });
        return response()->json($students);
    }


    /**
     * mark attedance
     */
    public function mark(Request $request){
        date_default_timezone_set('Africa/Nairobi');
        $lecture = Attendance::where('lecture',$request->lecture)->where('student',$request->regNo)->first();
        logger()->info($lecture == null);
        if($lecture == null){
            Attendance::create([
                'lecture' => $request->lecture,
                'student'=>$request->regNo,
                'clockIn' => date('Y-m-d H:i:s')
            ]);
            return response()->json([
                'message' => "present",
                'student' => Student::where('regNo',$request->regNo)->get()->map(function($student){
                    return [
                        'name' => $student->name,
                        'regNo' => $student->regNo,
                        'clockIn' => $student->clockIn,
                    ];
                }),
                'students' => $this->start(new Request($request->data))
            ]);
        }else{
            $data = [
                'faculty' =>$request->data['faculty'],
                'department' =>$request->data['department'],
                'course' =>$request->data['course'],
            ];
            return response()->json([
                'message' => "already present",
                'student' => Student::where('regNo',$request->regNo)->get()->map(function($student){
                    return [
                        'name' => $student->name,
                        'regNo' => $student->regNo,
                        'clockIn' => $student->clockIn,
                    ];
                }),
                'students' => $this->start(new Request($request->data))->original
            ]);
        }
    }
}
