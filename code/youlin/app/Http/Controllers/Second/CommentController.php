<?php
/**
 * Created by PhpStorm.
 * User: Kun
 * Date: 2020/5/17 4:46
 */


namespace App\Http\Controllers\Second;


use App\Http\Controllers\Controller;
use App\Models\AssentHistory;
use App\Models\Comment;
use App\Models\User;
use App\Models\UserTime;
use Illuminate\Http\Request;

class CommentController extends Controller
{

    // 点赞
    public function assent(Request $request)
    {
        $cid = $request->input('id');

        $model = AssentHistory::query()->firstOrNew([
            'user_id'    => $this->userId(),
            'comment_id' => $cid,
            'time_id'    => $request->input('time_id'),
        ]);

        if ($model->id) {
            return $this->error('不要重复点赞哦~');
        }

        $model->save();
        Comment::query()->whereKey($cid)->increment('assent');

        return $this->success();
    }

    // 取消点赞
    public function assentCancel(Request $request)
    {
        $cid = $request->input('id');

        $model = AssentHistory::query()->firstOrNew([
            'user_id'    => $this->userId(),
            'comment_id' => $cid,
        ]);

        if (!$model->id) {
            return $this->success();
        }

        $model->delete();
        Comment::query()->whereKey($cid)->decrement('assent');

        return $this->success();
    }

    // 评论详情
    public function index(Request $request)
    {
        $timeId   = $request->input('id');
        $comments = Comment::query()->where('time_id', $timeId)->orderBy('id')->get();

        return $this->success($this->parseTree($comments, $timeId));
    }

    private function parseTree($comments, $timeId)
    {
        $result = [];
        $has    = AssentHistory::query()->where('user_id', $this->userId())->where('time_id', $timeId)->pluck('comment_id');
        foreach ($comments as $comment) {
            $tmp = [
                'assented'   => $has->contains($comment->id) ? '已经赞过' : '没赞过',
                'comment_id' => $comment->id,
                'nickname'   => $comment->nickname,
                'header'     => $comment->header,
                'content'    => $comment->content,
                'assent'     => $comment->assent,
                'created_at' => $comment->created_at->toDateTimeString(),
            ];

            if ($comment->revert_id == 0) {
                $result[$comment->id] = $tmp;
            } else {
                $result[$comment->revert_id]['revert'][] = $tmp;
            }
        }

        return $result;
    }

    public function store(Request $request)
    {
        $timeId = $request->post('id');
        $user   = User::query()->find($this->userId());

        $comment = new Comment([
            'time_id'   => $timeId,
            'user_id'   => $user->user_id,
            'nickname'  => $user->nickname,
            'header'    => $user->headimgurl,
            'content'   => $request->post('content', ''),
            'revert_id' => $request->post('comment_id', 0),
        ]);

        $comment->save();

        UserTime::query()->whereKey($timeId)->increment('comment');

        return $this->success();
    }

}
