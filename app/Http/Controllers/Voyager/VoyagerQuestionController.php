<?php

namespace App\Http\Controllers\Voyager;

use App\Models\Answer;
use App\Models\Question;
use App\Models\TranslationEntry;
use App\Models\TranslationLanguage;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use TCG\Voyager\Facades\Voyager;
use Rny\ZhConverter\ZhConverter;
use Webpatser\Uuid\Uuid;
use Illuminate\Support\Facades\DB;

class VoyagerQuestionController extends VoyagerBreadController
{
    protected $fillLanguage = ['zh_TW', 'zh_HK'];

    public function index(Request $request)
    {
        return parent::index($request); // TODO: Change the autogenerated stub
    }

    public function create(Request $request)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('add', app($dataType->model_name));

        $languages = TranslationLanguage::all();

        // Check if BREAD is Translatable
        $isModelTranslatable = true;

        $columns = [];
        foreach ($languages as $language) {
            if (in_array($language->locale, $this->fillLanguage)) {
                continue;
            }

            $column = [
                'title' => $language->title,
                'name' => [
                    'question'=> 'question_' . $language->locale,
                    'answer' => 'answer_' . $language->locale
                ],
                'question' => '',
                'answers' => '',
                'lang' => $language->locale
            ];

            $columns[] = $column;
        }
        $columns = json_encode($columns);

        $view = 'voyager::bread.edit-add';

        if (view()->exists("voyager::$slug.edit-add")) {
            $view = "voyager::$slug.edit-add";
        }

        return Voyager::view($view, compact('dataType', 'languages', 'isModelTranslatable', 'columns'));
    }

    public function store(Request $request)
    {
        $token = $request->input('token');
        if (empty($request->session()->get($token))) {
            session()->flash('danger', '重复添加');
            return redirect()->route('voyager.questions.index');
        }

        $questions = $this->handleQuestions($request->columns);

//        print_r($questions);exit;

        if ($questions->isEmpty()) {
            session()->flash('danger', '请检查输入内容');
            return redirect()->back()->withInput();
        }

        DB::beginTransaction();
        try{
            $title = Uuid::generate(4)->string;
//            $question_id = DB::table('questions')->insertGetId([
//                'title' => $title,
//                'group_id' => $request->group_id ? $request->group_id : 1,
//                'created_at' => Carbon::now(),
//                'created_at' => Carbon::now()
//            ]);
            $question = Question::create([
                'title' => $title,
                'group_id' => $request->group_id ? $request->group_id : 1,
            ]);

            $translations = [];
            $translationIds = $this->generateUuid(count($questions[0]['answers']));

            foreach ($questions as $key => $item) {
                $translations[] = [
                    'translation_id' => $title,
                    'lang' => $item['lang'],
                    'value' => $item['title'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];

                foreach ($item['answers'] as $k =>  $val) {
                    if ($key === 0) {
                        $answer = Answer::create([
                            'title' => $translationIds[$k],
                            'question_id' => $question->id,
                            'score' => $val['score']
                        ]);
                    }

                    $translations[] = [
                        'translation_id' => $translationIds[$k],
                        'lang' => $item['lang'],
                        'value' => $val['title'],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ];
                }
            }

            DB::table('translation_entries')->insert($translations);

            DB::commit();
        }catch (QueryException $e) {
            DB::rollBack();
            session()->flash('danger', '添加到数据库失败'. $e->getMessage());
            return redirect()->back()->withInput();
        }

        session()->flash('success', '添加成功');
        return redirect()->route('voyager.questions.index');
    }

    protected function handleQuestions($columns)
    {
        $columns = json_decode($columns);
        $questions = [];
        $question_cn = [];
        foreach ($columns as $column) {
            $question = [
                'title' => $column->question,
                'answers' => $column->answers,
                'lang' => $column->lang
            ];

            $questions[] = $question;

            if ($column->lang == 'zh_CN') {
                $question_cn = $question;
            }
        }

        if (!empty($question_cn)) {
            $questions[] = [
                'title' => ZhConverter::zh2TW($question_cn['title']),
                'answers' => ZhConverter::zh2TW($question_cn['answers']),
                'lang' => 'zh_TW'
            ];
            $questions[] = [
                'title' => ZhConverter::zh2HK($question_cn['title']),
                'answers' => ZhConverter::zh2HK($question_cn['answers']),
                'lang' => 'zh_HK'
            ];
        }

        foreach ($questions as &$question) {
            $question['answers'] = $this->handleAnswers($question['answers']);
        }
        unset($question);

        return collect($questions);
    }

    protected function handleAnswers($answers)
    {
        $answers = explode(',', $answers);
        if (is_array($answers)) {

            $answers = collect($answers)->reject(function ($value, $key) {
                return empty($value);
            })->map(function($item, $key) {
                $answer = explode(':', $item);

                $return = [];
                if (!empty($answer[0]) && !empty($answer[1])) {
                    $return['title'] = $answer[0];
                    $return['score'] = $answer[1];
                }

                return collect($return);
            })->reject(function($value, $key){
                return empty($value);
            });
        }

        return $answers;
    }

    protected function generateUuid($num = 1)
    {
        $uuids = [];
        for ($i = 0; $i < $num; $i++){
            $uuids[] = Uuid::generate(4)->string;
        }

        return $uuids;
    }
}
