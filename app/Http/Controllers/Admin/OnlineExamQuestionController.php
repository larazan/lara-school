<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\ClassSection\ClassSectionInterface;
use App\Repositories\ClassSubject\ClassSubjectInterface;
use App\Repositories\OnlineExamQuestion\OnlineExamQuestionInterface;
use App\Repositories\OnlineExamQuestionOption\OnlineExamQuestionOptionInterface;
use App\Repositories\SubjectTeacher\SubjectTeacherInterface;
use App\Services\BootstrapTableService;
use App\Services\CachingService;
use App\Services\ResponseService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class OnlineExamQuestionController extends Controller
{
    private SubjectTeacherInterface $subjectTeacher;
    private ClassSectionInterface $classSection;
    private OnlineExamQuestionInterface $onlineExamQuestion;
    private OnlineExamQuestionOptionInterface $onlineExamQuestionOption;
    private CachingService $cache;
    private ClassSubjectInterface $classSubjects;

    public function __construct(SubjectTeacherInterface $subjectTeacher, ClassSectionInterface $classSection, OnlineExamQuestionInterface $onlineExamQuestion, OnlineExamQuestionOptionInterface $onlineExamQuestionOption, CachingService $cache, ClassSubjectInterface $classSubjects) {
        $this->subjectTeacher = $subjectTeacher;
        $this->classSection = $classSection;
        $this->onlineExamQuestion = $onlineExamQuestion;
        $this->onlineExamQuestionOption = $onlineExamQuestionOption;
        $this->cache = $cache;
        $this->classSubjects = $classSubjects;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        ResponseService::noFeatureThenRedirect('Exam Management');
        ResponseService::noPermissionThenRedirect('online-exam-questions-list');
        // $subjectTeachers = $this->subjectTeacher->builder()->with('subject:id,name,type')->get();

        $subjectTeachers = array();
        $classSubjects = array();
        if (Auth::user()->hasRole('School Admin')) {
            $classSubjects = $this->classSubjects->builder()->with('subject')->get();
        } else {
            $subjectTeachers = $this->subjectTeacher->builder()->with('subject')->get();    
        }
        $classSections = $this->classSection->builder()->with('class.medium', 'class.stream', 'section', 'medium')->get();

        return response(view('online_exam.class_questions', compact('classSections', 'subjectTeachers','classSubjects')));
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
        // ResponseService::noPermissionThenRedirect('online-exam-questions-create');
        // $validator = Validator::make($request->all(), ['class_id' => 'required', 'subject_id' => 'required', 'question_type' => 'required|in:0,1', 'question' => 'required_if:question_type,0', 'option.*' => 'required_if:question_type,0', 'equestion' => 'required_if:question_type,1', 'eoption.*' => 'required_if:question_type,1', 'answer.*' => 'required_if:question_type,0', 'image' => 'nullable|mimes:jpeg,png,jpg|image|max:3048',], ['question.required_if' => __('question_is_required'), 'option.*.required_if' => __('All options are required'), 'equestion.required_if' => __('question_is_required'), 'eoption.*.required_if' => __('All options are required'),]);

        // if ($validator->fails()) {
        //     ResponseService::errorResponse($validator->errors()->first());
        // }

        ResponseService::noFeatureThenRedirect('Exam Management');
        ResponseService::noPermissionThenRedirect('online-exam-questions-create');
        try {
            DB::beginTransaction();

            $onlineExamQuestionData = array(
                'class_section_id' => $request->class_section_id,
                'class_subject_id' => $request->class_subject_id,
                'question'         => htmlspecialchars($request->question, ENT_QUOTES | ENT_HTML5),
                'image_url'        => $request->image,
                'note'             => $request->note,
                'last_edited_by'   => Auth::user()->id
            );
            $onlineExamQuestion = $this->onlineExamQuestion->create($onlineExamQuestionData);

            $onlineExamOptionData = array();
            foreach ($request->option_data as $key => $optionValue) {
                $onlineExamOptionData[$key] = array(
                    'question_id' => $onlineExamQuestion->id,
                    'option'      => htmlspecialchars($optionValue['option'], ENT_QUOTES | ENT_HTML5),
                    'is_answer'   => 0, // Initialize is_answer to 0
                );
                foreach ($request->answer as $answerValue) {
                    if ($optionValue['number'] == $answerValue) {
                        $onlineExamOptionData[$key]['is_answer'] = 1; // Set is_answer to 1 if a match is found
                    }
                }
            }
            $this->onlineExamQuestionOption->createBulk($onlineExamOptionData);
            DB::commit();
            ResponseService::successResponse('Data Stored Successfully');
        } catch (Throwable $e) {
            DB::rollback();
            ResponseService::logErrorResponse($e, "Online Exam Question Controller -> Store method");
            ResponseService::errorResponse();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        ResponseService::noFeatureThenRedirect('Exam Management');
        ResponseService::noPermissionThenRedirect('online-exam-questions-list');
        $offset = request('offset', 0);
        $limit = request('limit', 10);
        $sort = request('sort', 'id');
        $order = request('order', 'ASC');
        $search = request('search');
        $subject_id = request('subject_id');

        $sql = $this->onlineExamQuestion->builder()->with('options', 'class_section', 'class_subject.subject')
            ->where(function ($query) use ($search, $subject_id) {
                $query->when($search, function ($query) use ($search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('id', 'LIKE', "%$search%")
                            ->orWhere('question', 'LIKE', "%$search%")
                            ->orWhere('created_at', 'LIKE', "%" . date('Y-m-d H:i:s', strtotime($search)) . "%")
                            ->orWhere('updated_at', 'LIKE', "%" . date('Y-m-d H:i:s', strtotime($search)) . "%")
                            ->orWhereHas('options', function ($p) use ($search) {
                                $p->where('option', 'LIKE', "%$search%");
                            });
                    });
                })->when(request('class_section_id'), function ($query) {
                    $query->where('class_section_id', request('class_section_id'));
                })->when(request('class_subject_id'), function ($query) {
                    $query->where('class_subject_id', request('class_subject_id'));
                })->when($subject_id, function($q) use($subject_id) {
                    $q->where('class_subject_id', $subject_id);
                });
            });

        $total = $sql->count();

        $sql->orderBy($sort, $order)->skip($offset)->take($limit);
        $res = $sql->get();
        $bulkData = array();
        $bulkData['total'] = $total;
        $rows = array();
        $tempRow = array();
        $no = 1;
        foreach ($res as $row) {
            // data for options which not answers
            $operate = BootstrapTableService::button('fa fa-edit', route('online-exam-question.edit', $row->id), ['btn-gradient-primary'], ['title' => 'Edit']); // Timetable Button
            $operate .= BootstrapTableService::trashButton(route('online-exam-question.destroy', $row->id));

            $tempRow['online_exam_question_id'] = $row->id;
            $tempRow['no'] = $no++;
            $tempRow['class_section_id'] = $row->class_section_id;
            $tempRow['class_name'] = $row->class_section_with_medium;
            $tempRow['class_subject_id'] = $row->class_subject_id;
            $tempRow['subject_name'] = $row->subject_with_name;
            $tempRow['question'] = "<div class='equation-editor-inline' contenteditable=false>" . htmlspecialchars_decode($row->question) . "</div>";
            $tempRow['question_row'] = htmlspecialchars_decode($row->question);
            //options data
            $tempRow['options'] = array();
            $tempRow['answers'] = array();
            foreach ($row->options as $options) {
                $tempRow['options'][] = array(
                    'id'         => $options->id,
                    'option'     => "<div class='equation-editor-inline' contenteditable=false>" . $options->option . "</div>",
                    'option_row' => $options->option
                );
                if ($options->is_answer) {
                    $tempRow['answers'][] = array(
                        'id'         => $options->id,
                        'answer'     => "<div class='equation-editor-inline' contenteditable=false>" . $options->option . "</div>",
                        'option_row' => $options->option
                    );
                }
            }
            $tempRow['image'] = $row->image_url;
            $tempRow['note'] = htmlspecialchars_decode($row->note);
            $tempRow['created_at'] = $row->created_at;
            $tempRow['updated_at'] = $row->updated_at;
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
        ResponseService::noFeatureThenRedirect('Exam Management');
        ResponseService::noPermissionThenRedirect('online-exam-questions-edit');
        $onlineExamQuestion = $this->onlineExamQuestion->findById($id, ['*'], ['options', 'class_section', 'class_subject.subject']);

        return response(view('online_exam.edit_class_questions', compact('onlineExamQuestion')));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // $validator = Validator::make($request->all(), ['edit_class_id' => 'required', 'edit_subject_id' => 'required', 'edit_question' => 'required_if:edit_question_type,0', 'edit_option.*.option' => 'required_if:edit_question_type,0', 'edit_equestion' => 'required_if:edit_question_type,1', 'edit_eoption.*.option' => 'required_if:edit_question_type,1', 'edit_image' => 'nullable|mimes:jpeg,png,jpg|image|max:3048',], ['edit_question.required_if' => __('question_is_required'), 'edit_option.*.option.required_if' => __('All options are required'), 'edit_equestion.required_if' => __('question_is_required'), 'edit_eoption.*.option.required_if' => __('All options are required'),]);
        // if ($validator->fails()) {
        //     ResponseService::errorResponse($validator->errors()->first());
        // }
        ResponseService::noFeatureThenRedirect('Exam Management');
        ResponseService::noPermissionThenSendJson('online-exam-questions-edit');
        try {
            DB::beginTransaction();
            $onlineExamQuestionData = array(
                'question'       => htmlspecialchars($request->question, ENT_QUOTES | ENT_HTML5),
                'note'           => $request->note,
                'last_edited_by' => Auth::user()->id,
            );
            if (!empty($request->image)) {
                $onlineExamQuestionData['image_url'] = $request->image;
            }
            $onlineExamQuestion = $this->onlineExamQuestion->update($id, $onlineExamQuestionData);

            $onlineExamOptionData = array();
            foreach ($request->option_data as $key => $optionValue) {
                $onlineExamOptionData[$key] = array(
                    'id'          => $optionValue['id'],
                    'question_id' => $onlineExamQuestion->id,
                    'option'      => htmlspecialchars($optionValue['option'], ENT_QUOTES | ENT_HTML5),
                    'is_answer'   => 0, // Initialize is_answer to 0
                );
                foreach ($request->answer as $answerValue) {
                    if ($optionValue['number'] == $answerValue) {
                        $onlineExamOptionData[$key]['is_answer'] = 1; // Set is_answer to 1 if a match is found
                        break; // Break the loop as we've found a match
                    }
                }
            }
            $this->onlineExamQuestionOption->upsert($onlineExamOptionData, ["id"], ["question_id", "option", "is_answer"]);
            DB::commit();
            ResponseService::successResponse('Data Updated Successfully');
        } catch (Throwable $e) {
            DB::rollback();
            ResponseService::logErrorResponse($e, "Online Exam Question Controller -> Update method");
            ResponseService::errorResponse();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        ResponseService::noFeatureThenRedirect('Exam Management');
        ResponseService::noPermissionThenSendJson('online-exam-questions-delete');
        try {
            $this->onlineExamQuestion->deleteById($id);
            ResponseService::successResponse('Data Deleted Successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "Online Exam Question Controller -> Destroy method", 'cannot_delete_because_data_is_associated_with_other_data');
            ResponseService::errorResponse();
        }
    }

    public function removeOptions($id) {
        ResponseService::noFeatureThenRedirect('Exam Management');
        ResponseService::noPermissionThenRedirect('online-exam-questions-delete');
        try {
            $this->onlineExamQuestionOption->deleteById($id);
            ResponseService::successResponse('Data Deleted Successfully');
        } catch (Throwable $e) {
            ResponseService::logErrorResponse($e, "Online Exam Question Controller -> Remove Options method");
            ResponseService::errorResponse();
        }
    }
}
