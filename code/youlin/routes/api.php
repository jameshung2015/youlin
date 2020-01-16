<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/upload/images', 'UploadController@images');    //上传文件
Route::post('/upload/base64', 'UploadController@base64');    //上传base64图片

// 我的页面
Route::group(['middleware' => 'login'], function () {

    Route::get('/user/payment', 'UserController@payment');                   // 用户是否购买过

    Route::get('/user/info', 'UserController@userInfo');
    Route::get('/user/week', 'UserController@week');
    // 20191201
    Route::get('/family', 'FamilyController@index');                         // 家庭列表
    Route::get('/family/del', 'FamilyController@del');                         // 删除群组
    Route::post('/family/add', 'FamilyController@add');                      // 添加家庭
    Route::post('/family/rename', 'FamilyController@FamilyRename');          // 家庭改名字
    Route::post('/family/sub', 'FamilyController@subMember');                # 家庭减员
    Route::post('/family/add/member', 'FamilyController@addFamilyMember');   # 家庭增员

    Route::post('/member/remark', 'MemberController@remark');      //修改备注
    Route::post('/member/add', 'MemberController@add');            //添加好友
    Route::post('mobile', 'WeChatController@mobile');              //用户手机号(小程序)
    Route::post('/user/info', 'WeChatController@smallAuth');       //用户加密信息(小程序)

    Route::get('/time/show', 'ActiveController@show');         // 活动详情

    // 20191208
    Route::post('/active/create', 'ActiveController@create');      //发布自己的活动
    Route::post('/active/edit', 'ActiveController@edit');          //编辑自己的活动
    Route::post('/subject/create', 'SubjectController@create');    //发布自己的话题
    Route::post('/subject/edit', 'SubjectController@edit');        //编辑自己的话题

    Route::get('/time/log', 'UserController@timeLog');       //我的时光
    Route::post('/time/comment', 'UserController@comment');  //评论


    Route::prefix('system')->group(function () {
        Route::get('/subject/all', 'SystemController@subjectAll');       // 系统话题类别
        Route::get('/subject/index', 'SystemController@subjectIndex');   // 系统话题分类
        Route::get('/subject/show', 'SystemController@subjectShow');     // 系统话题详情
        Route::post('/subject/party', 'SystemController@subjectParty');  //发布系统话题
        Route::get('/active/index', 'SystemController@activeIndex');     //系统活动分类
        Route::get('/active/all', 'SystemController@activeAll');         //系统活动列表
        Route::post('/active/party', 'SystemController@activeParty');    //发布系统活动
        Route::get('/active/click', 'SystemController@click');    //发布系统活动
    });

});


