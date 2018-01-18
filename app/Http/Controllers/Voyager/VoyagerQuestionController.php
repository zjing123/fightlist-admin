<?php

namespace App\Http\Controllers\Voyager;

use App\Libaries\Youdao\Translation;
use App\Models\Answer;
use App\Models\Question;
use App\Models\TranslationEntry;
use App\Models\TranslationLanguage;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use TCG\Voyager\Database\Schema\SchemaManager;
use TCG\Voyager\Facades\Voyager;
use Rny\ZhConverter\ZhConverter;
use Webpatser\Uuid\Uuid;
use Illuminate\Support\Facades\DB;

class VoyagerQuestionController extends VoyagerBreadController
{
    const DEFAULT_SCORE = 1;

    protected $fillLanguage = ['zh_TW', 'zh_HK'];

    public function index(Request $request)
    {
        // GET THE SLUG, ex. 'posts', 'pages', etc.
        $slug = $this->getSlug($request);

        // GET THE DataType based on the slug
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('browse', app($dataType->model_name));

        $getter = $dataType->server_side ? 'paginate' : 'get';

        $search = (object) ['value' => $request->get('s'), 'key' => $request->get('key'), 'filter' => $request->get('filter')];
        $searchable = $dataType->server_side ? array_keys(SchemaManager::describeTable(app($dataType->model_name)->getTable())->toArray()) : '';
        $orderBy = $request->get('order_by');
        $sortOrder = $request->get('sort_order', null);

        $query = DB::table('questions')
            ->leftJoin('translation_entries', 'questions.title', '=', 'translation_entries.translation_id')
            ->where('translation_entries.lang', 'zh_CN');

        $query = Question::with('entries');

        if ($search->value && $search->key && $search->filter) {
            $search_filter = ($search->filter == 'equals') ? '=' : 'LIKE';
            $search_value = ($search->filter == 'equals') ? $search->value : '%'.$search->value.'%';
            $query->where($search->key, $search_filter, $search_value);
        }

        if ($orderBy && in_array($orderBy, $dataType->fields())) {
            $querySortOrder = (!empty($sortOrder)) ? $sortOrder : 'DESC';
            $dataTypeContent = call_user_func([
                $query->orderBy($orderBy, $querySortOrder),
                $getter,
            ]);
        } else {
            $dataTypeContent = call_user_func([
                $query,
                $getter,
            ]);
        }

        // Check if BREAD is Translatable
        if (($isModelTranslatable = is_bread_translatable(false))) {
            $dataTypeContent->load('translations');
        }

        // Check if server side pagination is enabled
        $isServerSide = isset($dataType->server_side) && $dataType->server_side;

        $view = 'voyager::bread.browse';

        if (view()->exists("voyager::$slug.browse")) {
            $view = "voyager::$slug.browse";
        }

        return Voyager::view($view, compact(
            'dataType',
            'dataTypeContent',
            'isModelTranslatable',
            'search',
            'orderBy',
            'sortOrder',
            'searchable',
            'isServerSide'
        ));
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
                'lang' => $language->locale,
                'showAnswer' => $language->locale == 'en' ? true : true
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
        print_r($questions);exit;

        if ($questions->isEmpty()) {
            session()->flash('danger', '请检查输入内容');
            return redirect()->back()->withInput();
        }

        DB::beginTransaction();
        try{
            $title = Uuid::generate(4)->string;

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

    public function show(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Compatibility with Model binding.
        $id = $id instanceof Model ? $id->{$id->getKeyName()} : $id;

        // Replace relationships' keys for labels and create READ links if a slug is provided.
        $dataTypeContent = Question::with(['answers', 'entries'])->where('id', $id)->first();

        $languages = TranslationLanguage::all();

        // If a column has a relationship associated with it, we do not want to show that field
        $this->removeRelationshipField($dataType, 'read');

        // Check permission
        $this->authorize('read', $dataTypeContent);

        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable($dataTypeContent);

        $view = 'voyager::bread.read';

        if (view()->exists("voyager::$slug.read")) {
            $view = "voyager::$slug.read";
        }

        return Voyager::view($view, compact('dataType', 'languages', 'dataTypeContent', 'isModelTranslatable'));
    }

    public function edit(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Compatibility with Model binding.
        $id = $id instanceof Model ? $id->{$id->getKeyName()} : $id;

        // If a column has a relationship associated with it, we do not want to show that field
        $this->removeRelationshipField($dataType, 'edit');

        $dataTypeContent = null;

        // Check permission
        $this->authorize('edit', $dataTypeContent);

        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable($dataTypeContent);

        $view = 'voyager::bread.edit-add';

        if (view()->exists("voyager::$slug.edit-add")) {
            $view = "voyager::$slug.edit-add";
        }

        return Voyager::view($view, compact('dataType', 'dataTypeContent', 'isModelTranslatable'));
    }

    public function destroy(Request $request, $id)
    {
        $ids = [];
        if (empty($id)) {
            // Bulk delete, get IDs from POST
            $ids = explode(',', $request->ids);
        } else {
            // Single item delete, get ID from URL or Model Binding
            $ids[] = $id instanceof Model ? $id->{$id->getKeyName()} : $id;
        }

        DB::beginTransaction();
        try{
            foreach ($ids as $id) {
                $question = Question::find($id);

                $answers = Answer::where('question_id', $question->id)->get();
                if ($answers) {
                    foreach ($answers as $answer) {
                        TranslationEntry::where('translation_id', $answer->title)->delete();
                        $answer->delete();
                    }
                }

                TranslationEntry::where('translation_id', $question->title)->delete();
                $question->delete();
            }

            DB::commit();
        } catch (QueryException $ex) {
            throw $ex;
            DB::rollback();
            session()->flash('danger', "删除失败!<br/>" . $ex->getMessage());
            return redirect()->route('voyager.questions.index');
        }

        session()->flash('success', "删除成功!");
        return redirect()->route('voyager.questions.index');
    }

    public function editAnswer(Request $request, $question_id, $answer_id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        $question = Question::with(['entries' => function($query) {
            $query->where('lang', 'zh_CN')->first();
        }])->where('id', $question_id)->first();

        $languages = TranslationLanguage::all();

        $answer = Answer::with('entries')->where('id', $answer_id)->first();

        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable(Question::find($question_id));

        $view = 'voyager::questions.edit-answer';

        return Voyager::view($view, compact(
            'dataType',
            'question',
            'answer',
            'languages',
            'isModelTranslatable'
        ));
    }

    public function updateAnswer(Request $request, $question_id, $answer_id)
    {
        $language = TranslationLanguage::all();
        $translation_id = $request->translation_id;

        if (empty($translation_id)) {
            session()->flash('success', 'translation_id无效!');
            return redirect()->route('voyager.questions.show', $question_id);
        }

        if (empty($request->answer)) {
            session()->flash('danger', '参数错误!');
            return redirect()->route('voyager.questions.show', $question_id);
        }

        $translations = TranslationEntry::where('translation_id', $translation_id)->get();
        if (empty($translations)) {
            session()->flash('danger', 'translation_id不存在!');
            return redirect()->route('voyager.questions.show', $question_id);
        }

        if (is_array($request->answer)) {
            foreach ($request->answer as $lang => $val) {
                TranslationEntry::where('translation_id', $translation_id)
                    ->where('lang', $lang)
                    ->update(['value' => $val]);
            }
        }

        session()->flash('success', '修改成功');
        return redirect()->route('voyager.questions.show', $question_id);
    }

    public function destroyAnswer(Request $request, $id)
    {
        if (empty($id)) {
            session()->flash('danger', '参数错误!');
            return redirect()->route('voyager.questions.show', $request->question_id);
        }

        DB::beginTransaction();
        try{
            $answer = Answer::find($id);

            TranslationEntry::where('translation_id', $answer->title)->delete();
            $answer->delete();

            DB::commit();
        } catch (QueryException $ex) {
            throw $ex;
            DB::rollback();
            session()->flash('danger', "删除失败!<br/>" . $ex->getMessage());
            return redirect()->route('voyager.questions.show', $request->question_id);
        }

        session()->flash('success', "删除成功!");
        return redirect()->route('voyager.questions.show', $request->question_id);
    }

    protected function handleQuestions($columns)
    {
        $columns = json_decode($columns);
        $questions = [];
        $question_cn = [];
        $question_en = [];
        foreach ($columns as $column) {
            if($column->lang == 'en') {
                $question_en['title'] = $column->question;
                $question_en['lang'] = $column->lang;
                continue;
            }

            $question = [
                'title' => $column->question,
                'answers' => $column->answers,
                'lang' => $column->lang
            ];

            if ($column->lang == 'zh_CN') {
                $question_cn = $question;
            }

            $questions[] = $question;
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

//            if (!empty($question_en)) {
//                $translate = new Translation();
//                $translation = $translate->translate(str_replace("|", "|\n", $question_cn['answers']), 'zh-CHS', 'EN');
//                print_r($translation);
//                $question_en['answers'] = $translation == null ? '' : trim(str_replace("\n", '', $translation[0]));
//                print_r($question_en['answers']);exit;
//                $questions[] = $question_en;
//            }

            if (!empty($question_en)) {
                $question_en['answers'] = $this->handleEnAnswers($question_cn['answers']);

                print_r($question_en['answers']);exit;
            }

        }

        foreach ($questions as &$question) {
            $question['answers'] = $this->handleAnswers($question['answers']);
        }
        unset($question);

        return collect($questions);
    }

    protected function handleEnAnswers($answers)
    {
        //切割字符串
        $answers = preg_split("/[,|]+/", $answers);
        $answers = array_unique($answers);

        if (is_array($answers)) {
            $translate = new Translation();
            $answers = collect($answers)
                ->unique()
                ->reject(function ($value, $key) {
                    return empty($value);
                })->map(function($item, $key) use ($translate) {
                    $answer = explode(':', $item);
                    $return = [];

                    if (is_array($answer)) {
                        if (!empty($answer[0])) {

                            $translation = $translate->translate(trim($answer[0]), 'zh-CHS', 'EN');
                            if(!empty($answer[1])) {
                                $return['title'] = trim($translation[0]);
                                $return['score'] = trim($answer[1]);
                            } else {
                                $return['title'] = trim($translation[0]);
                                $return['score'] = self::DEFAULT_SCORE;
                            }
                        }
                    }

                    return collect($return);
                })->reject(function($value, $key){
                    return empty($value);
                });
        }

        return $answers;
    }

    protected function handleAnswers($answers)
    {
        //切割字符串
        $answers = preg_split("/[,|]+/", $answers);
        $answers = array_unique($answers);

        if (is_array($answers)) {
            $answers = collect($answers)
                ->unique()
                ->reject(function ($value, $key) {
                    return empty($value);
                })->map(function($item, $key) {
                    $answer = explode(':', $item);
                    $return = [];

                    if (is_array($answer)) {
                        if (!empty($answer[0])) {
                            if(!empty($answer[1])) {
                                $return['title'] = trim($answer[0]);
                                $return['score'] = trim($answer[1]);
                            } else {
                                $return['title'] = trim($answer[0]);
                                $return['score'] = self::DEFAULT_SCORE;
                            }
                        }
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
