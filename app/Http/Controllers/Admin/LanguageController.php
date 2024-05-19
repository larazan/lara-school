<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\Languages\LanguageInterface;
use App\Services\BootstrapTableService;
use App\Services\CachingService;
use App\Services\ResponseService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Throwable;

class LanguageController extends Controller
{
    private LanguageInterface $language;
    private CachingService $cache;

    public function __construct(LanguageInterface $language, CachingService $cachingService) {
        $this->language = $language;
        $this->cache = $cachingService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        ResponseService::noPermissionThenRedirect('language-create');
        return view('settings.language_setting');
    }

    public function language_sample() {
        ResponseService::noPermissionThenRedirect('language-create');
        $filePath = base_path("resources/lang/en.json");
        $headers = ['Content-Type: application/json'];
        $fileName = 'language.json';
        if (File::exists(base_path("resources/lang/en.json"))) {
            return response()->download($filePath, $fileName, $headers);
        }

        ResponseService::errorResponse();
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
        ResponseService::noPermissionThenRedirect('language-create');
        $request->validate([
            'name' => 'required',
            'code' => 'required|unique:languages,code',
            'file' => 'required|mimes:json',
        ]);

        try {

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $filename = $request->code . '.' . $file->getClientOriginalExtension();
                $file->move(base_path('resources/lang/'), $filename);
            }

            $languageData = array(
                'name'   => $request->name,
                'code'   => $request->code,
                'status' => 0,
                'is_rtl' => $request->rtl ?? 0,
                'file'   => $filename ?? NULL
            );

            $this->language->create($languageData);
            $this->cache->removeSystemCache(config('constants.CACHE.SYSTEM.LANGUAGE'));
            ResponseService::successResponse('Data Stored Successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "Language Controller -> Store method");
            ResponseService::errorResponse();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        ResponseService::noPermissionThenRedirect('language-list');
        $offset = request('offset', 0);
        $limit = request('limit', 10);
        $sort = request('sort', 'id');
        $order = request('order', 'DESC');
        $search = request('search');

        $sql = $this->language->builder()
            ->where(function ($query) use ($search) {
                $query->when('search', function ($query) use ($search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('id', 'LIKE', "%$search%")
                            ->orwhere('name', 'LIKE', "%$search%")
                            ->orwhere('code', 'LIKE', "%$search%")
                            ->orwhere('status', 'LIKE', "%$search%");
                    });
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
            $operate = "";
            if ($row->code != "en") {
                $operate .= BootstrapTableService::editButton(route('language.update', $row->id));
                $operate .= BootstrapTableService::deleteButton(route('language.destroy', $row->id));
            }
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        ResponseService::noPermissionThenRedirect('language-edit');
        $request->validate([
            'name' => 'required',
            'code' => 'required|unique:languages,code,' . $id,
        ]);

        try {
            $languageData = array(
                'name'   => $request->name,
                'code'   => $request->code,
                'is_rtl' => $request->rtl ?? 0,
            );

            $languageDB = $this->language->findById($id);
            if ($request->hasFile('file')) {
                $request->validate(['file' => 'required|mimes:json',]);
                if (File::exists(base_path("resources/lang/") . $languageDB->file)) {
                    File::delete(base_path("resources/lang/") . $languageDB->file);
                }
                $file = $request->file('file');
                $filename = $request->code . '.' . $file->getClientOriginalExtension();
                $file->move(base_path('resources/lang/'), $filename);
//                $language['file'] = $filename;
            }

            $this->language->update($id, $languageData);
            ResponseService::successResponse('Data Updated Successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "Language Controller -> Update method");
            ResponseService::errorResponse();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        ResponseService::noPermissionThenSendJson('language-delete');
        try {
            $this->language->deleteById($id);
            ResponseService::successResponse('Data Deleted Successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "Language Controller -> Delete method");
            ResponseService::errorResponse();
        }
    }

    public function set_language(Request $request) {
        Session::put('locale', $request->lang);
        Session::save();
        $language = $this->language->builder()->where('code', $request->lang)->first();
        Session::put('language', $language);
        app()->setLocale(Session::get('locale'));
        return redirect()->back();
    }
}
