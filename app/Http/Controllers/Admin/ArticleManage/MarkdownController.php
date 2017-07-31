<?php

namespace App\Http\Controllers\Admin\ArticleManage;

use App\Components\ApiResponse;
use App\Components\Qiniu;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleColumn;
use App\Models\ArticleMarkdown;
use App\Models\ArticleTag;
use HyperDown\Parser;
use Illuminate\Http\Request;

/**
 * Markdown
 * @menu index Markdown文章
 * @nodeTitle Markdown
 * @nodeName index 列表
 * @nodeName create 写文章
 * @nodeName store 保存
 * @nodeName show 详情编辑
 * @nodeName preview 预览
 * @nodeName update 更新
 * @nodeName destroy 删除
 */
class MarkdownController extends Controller
{

    /**
     * 文章列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $data = array(
            'paginate' => ArticleMarkdown::with('article')->orderby('id', 'desc')->paginate(),
        );

        return view('admin.article-manage.markdown.index', $data);
    }

    /**
     * 写文章
     * @param Qiniu $qiniu
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Qiniu $qiniu)
    {
        $callbackUrl = config('qiniu.callback_ueditor');
        $uploadToken = $qiniu->uploadToken($callbackUrl);
        $columns = ArticleColumn::homeColumns();
        $tags = ArticleTag::getAllTag();

        $data = array(
            'navLocation' => action('\\' . self::class . '@index'),
            'uploadToken' => $uploadToken,
            'columns' => $columns,
            'tags' => $tags
        );

        return view('admin.article-manage.markdown.create', $data);
    }

    /**
     * 保存文章
     * @param Request $request
     * @param Parser $parser
     * @return ApiResponse
     */
    public function store(Request $request, Parser $parser)
    {
        $this->trimInput(['content', 'title', 'author']);
        $status = implode(',', [Article::STATUS_RELEASE, Article::STATUS_DRAFT]);
        $type = implode(',', [Article::TYPE_ARTICLE, Article::TYPE_PAGE]);
        $coverType = implode(',', [Article::COVER_TYPE_NONE, Article::COVER_TYPE_SMALL, Article::COVER_TYPE_BIG]);
        $coverUrlRequire = implode(',', [Article::COVER_TYPE_SMALL, Article::COVER_TYPE_BIG]);
        $this->validate($request, array(
            'title' => ['required', 'max:100'],
            'author' => ['required', 'max:12'],
            'content' => ['required'],
            'status' => ['required', "in:{$status}"],
            'type' => ['required', "in:{$type}"],
            'tag' => ['array'],
            'column' => ['array'],
            'write_time' => ['required', 'date_format:Y-m-d'],
            'cover_type' => ['required', "in:{$coverType}"],
            'cover_url' => ["required_if:cover_type,{$coverUrlRequire}"]
        ));

        $data = $request->only(['title', 'status', 'type', 'author', 'write_time', 'cover_type', 'cover_url']);
        $data['user_id'] = \Auth::guard()->user()->getAuthIdentifier();
        $data['excerpt'] = str_excerpt($request->input('content'), 250);
        $column = $request->input('column');
        $tag = $request->input('tag');
        $markdown = $request->input('content');
        $content = $parser->makeHtml($markdown);

        \DB::transaction(function () use ($data, $column, $tag, $content, $markdown) {
            $article = (new Article())->add($data, $column, $tag, $content);
            ArticleMarkdown::create(['article_id' => $article->id, 'content' => $markdown]);
        });

        return ApiResponse::buildFromArray();
    }

    /**
     * 详情编辑
     * @param Qiniu $qiniu
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Qiniu $qiniu, $id)
    {
        $callbackUrl = config('qiniu.callback_ueditor');
        $uploadToken = $qiniu->uploadToken($callbackUrl);
        $columns = ArticleColumn::homeColumns();
        $tags = ArticleTag::getAllTag();
        /** @var ArticleMarkdown $model */
        $model = ArticleMarkdown::with('article', 'article.tags', 'article.columns', 'article.contents')->findOrFail($id);
        $data = array(
            'navLocation' => action('\\' . self::class . '@index'),
            'model' => $model,
            'form' => array(
                'title' => $model->article->title,
                'author' => $model->article->author,
                'content' => $model->content,
                'status' => $model->article->status,
                'type' => $model->article->type,
                'write_time' => substr($model->article->write_time, 0, 10),
                'cover_type' => $model->article->cover_type,
                'cover_url' => $model->article->cover_url,
                'column' => array_column($model->article->columns->toArray(), 'id'),
                'tag' => array_column($model->article->tags->toArray(), 'tag'),
            ),
            'columnIds' => array_column($model->article->columns->toArray(), 'id'),
            'uploadToken' => $uploadToken,
            'columns' => $columns,
            'tags' => $tags
        );

        return view('admin.article-manage.markdown.show', $data);
    }

    /**
     * 更新
     * @param Request $request
     * @param Parser $parser
     * @param $id
     * @return ApiResponse
     */
    public function update(Request $request, Parser $parser, $id)
    {
        /** @var ArticleMarkdown $model */
        $model = ArticleMarkdown::with('article')->findOrFail($id);
        $status = implode(',', [Article::STATUS_RELEASE, Article::STATUS_DRAFT]);
        $type = implode(',', [Article::TYPE_ARTICLE, Article::TYPE_PAGE]);
        $coverType = implode(',', [Article::COVER_TYPE_NONE, Article::COVER_TYPE_SMALL, Article::COVER_TYPE_BIG]);
        $coverUrlRequire = implode(',', [Article::COVER_TYPE_SMALL, Article::COVER_TYPE_BIG]);
        $this->validate($request, array(
            'title' => ['required', 'max:100'],
            'author' => ['required', 'max:12'],
            'content' => ['required'],
            'status' => ['required', "in:{$status}"],
            'type' => ['required', "in:{$type}"],
            'tag' => ['array'],
            'column' => ['array'],
            'write_time' => ['required', 'date_format:Y-m-d'],
            'cover_type' => ['required', "in:{$coverType}"],
            'cover_url' => ["required_if:cover_type,{$coverUrlRequire}"]
        ));

        $data = $request->only(['title', 'status', 'type', 'author', 'write_time', 'cover_type', 'cover_url']);
        $data['user_id'] = \Auth::guard()->user()->getAuthIdentifier();
        $data['excerpt'] = str_excerpt($request->input('content'), 250);
        $column = $request->input('column');
        $tag = $request->input('tag');
        $markdown = $request->input('content');
        $content = $parser->makeHtml($markdown);

        \DB::transaction(function () use ($model, $markdown, $data, $column, $tag, $content) {
            $model->update(['content' => $markdown]);
            $model->article->put($data, $column, $tag, $content);
        });

        return ApiResponse::buildFromArray();
    }

    /**
     * 删除
     * @param $id
     * @return ApiResponse
     */
    public function destroy($id)
    {
        /** @var ArticleMarkdown $model */
        $model = ArticleMarkdown::with('article')->findOrFail($id);

        Article::del($model->article->id, function() use ($model) {
            $model->delete();
        });

        return ApiResponse::buildFromArray();
    }

    /**
     * 预览
     * @param Request $request
     * @param Parser $parser
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function preview(Request $request, Parser $parser)
    {
        $request->merge(['content' => $parser->makeHtml($request->input('content'))]);

        $data = array(
            'article' => $request->all(),
            'pageName' => $request->input('title'),
            'navs' => array(),
            'stat' => array(
                'articleTotal' => '-',
                'columnTotal' => '-',
                'tagTotal' => '-',
            )
        );

        return view('blog.preview', $data);
    }

}