<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\LeaveMaster\LeaveMasterInterface;
use App\Repositories\SessionYear\SessionYearInterface;
use App\Services\BootstrapTableService;
use App\Services\CachingService;
use App\Services\ResponseService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class LeaveMasterController extends Controller
{
    private LeaveMasterInterface $leaveMaster;
    private SessionYearInterface $sessionYear;

    public function __construct(LeaveMasterInterface $leaveMaster, SessionYearInterface $sessionYear) {
        $this->leaveMaster = $leaveMaster;
        $this->sessionYear = $sessionYear;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        ResponseService::noPermissionThenRedirect('school-setting-manage');
        $sessionYear = $this->sessionYear->builder()->pluck('name','id');
        return view('leave.leave_master',compact('sessionYear'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        ResponseService::noPermissionThenRedirect('school-setting-manage');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        ResponseService::noPermissionThenRedirect('school-setting-manage');
        $request->validate([
            'leaves' => 'required|numeric',
            'holiday_days' => 'required',
            'session_year_id' => 'required|unique:leave_masters'
        ],[
            'session_year_id.unique' => 'This session year has already been taken.'
        ]);

        try {
            DB::beginTransaction();
            $day = implode(',',$request->holiday_days);
            $data = [
                'leaves' => $request->leaves,
                'holiday' => $day,
                'session_year_id' => $request->session_year_id,
            ];

            $this->leaveMaster->create($data);
            DB::commit();
            ResponseService::successResponse('Data Stored Successfully');
        } catch (\Throwable $e) {
            ResponseService::logErrorResponse($e, "LeaveMaster Controller -> Store Method");
            ResponseService::errorResponse();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        ResponseService::noPermissionThenRedirect('school-setting-manage');
        $offset = request('offset', 0);
        $limit = request('limit', 10);
        $sort = request('sort', 'id');
        $order = request('order', 'DESC');
        $search = request('search');

        $sql = $this->leaveMaster->builder()->with('session_year')
            ->when(request('session_year_id') != null, function ($query) use ($request) {
                $query->where('session_year_id', $request->session_year_id);
            })->where(function($q) use($search){
                $q->when($search, function ($query) use ($search) {
                    $query->where('leaves','LIKE', "%$search%")
                    ->orWhere('holiday','LIKE', "%$search%");
                });
            });
            

        $total = $sql->count();

        $sql->orderBy($sort, $order)->skip($offset)->take($limit);
        $res = $sql->get();

        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $no = 1;
        foreach ($res as $row) {
            $operate = BootstrapTableService::editButton(route('leave-master.update', $row->id));
            $operate .= BootstrapTableService::deleteButton(route('leave-master.destroy', $row->id));
            $tempRow = $row->toArray();
            $tempRow['no'] = $no++;
            $tempRow['operate'] = $operate;
            $rows[] = $tempRow;
        }
        $bulkData['rows'] = $rows;
        return response()->json($bulkData);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        ResponseService::noPermissionThenRedirect('school-setting-manage');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        ResponseService::noPermissionThenRedirect('school-setting-manage');
        $request->validate([
            'leaves' => 'required|numeric',
            'holiday_days' => 'required',
            'session_year_id' => 'required|unique:leave_masters,session_year_id,'.$id
        ],[
            'session_year_id.unique' => 'This session year has already been taken.'
        ]);

        try {
            DB::beginTransaction();
            $day = implode(',',$request->holiday_days);
            $data = [
                'leaves' => $request->leaves,
                'holiday' => $day,
                'session_year_id' => $request->session_year_id,
            ];

            $this->leaveMaster->update($id,$data);
            DB::commit();
            ResponseService::successResponse('Data Updated Successfully');
        } catch (\Throwable $e) {
            ResponseService::logErrorResponse($e, "LeaveMaster Controller -> Store Method");
            ResponseService::errorResponse();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        ResponseService::noPermissionThenRedirect('school-setting-manage');        
        try {
            $leaveMaster =$this->leaveMaster->findById($id);
            if(count($leaveMaster->leave)) {
                ResponseService::errorResponse('cannot_delete_because_data_is_associated_with_other_data');
            } else {
                $this->leaveMaster->deleteById($id);
            }
            ResponseService::successResponse('Data Deleted Successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "LeaveMaster Controller -> Delete Method");
            ResponseService::errorResponse();
        }
    }
}
