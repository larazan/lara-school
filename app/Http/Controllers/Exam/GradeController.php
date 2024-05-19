<?php

namespace App\Http\Controllers\Exam;

use App\Repositories\Grades\GradesInterface;
use App\Services\ResponseService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class GradeController extends Controller
{
    private GradesInterface $grades;

    public function __construct(GradesInterface $grades) {
        $this->grades = $grades;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        ResponseService::noFeatureThenRedirect('Exam Management');
        ResponseService::noPermissionThenRedirect('grade-create');
        $grades = $this->grades->all();
        return response(view('exams.grade', compact('grades')));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        ResponseService::noFeatureThenRedirect('Exam Management');
        ResponseService::noPermissionThenSendJson('grade-create');
        $request->validate([
            'grade_data' => 'required|array'
        ]);
        try {
            foreach ($request->grade_data as $data) {
                $gradesData = array(
                    'starting_range' => $data['starting_range'],
                    'ending_range'   => $data['ending_range'],
                    'grade'          => $data['grades'],
                    'created_at'     => now(),
                );
                $this->grades->updateOrCreate(['id' => $data['id'] ?? null], $gradesData);
            }
            ResponseService::successResponse('Data Updated Successfully');
        } catch (Throwable $e) {
            DB::rollBack();
            ResponseService::logErrorResponse($e, "Exam Controller -> Grade Create OR Update method");
            ResponseService::errorResponse();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        ResponseService::noFeatureThenRedirect('Exam Management');
        ResponseService::noPermissionThenSendJson('grade-delete');
        try {
            $this->grades->deleteById($id);
            ResponseService::successResponse('Data Deleted Successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "Exam Controller -> Grade Delete method");
            ResponseService::errorResponse();
        }
    }
}
