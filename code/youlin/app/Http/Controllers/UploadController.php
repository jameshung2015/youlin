<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;

class UploadController extends Controller
{
    protected $excel = ['xls', 'xlsx', 'csv'];
    protected $image = ['png', 'jpg', 'jpeg', 'bmp'];

    public function images(Request $request)
    {
        try {
            $file     = $this->checkImportFile($request, 'images', $this->image);
            $ymd      = date('Ymd');
            $destPath = $this->destPath('images/' . $ymd);

            $filename = sprintf('%s/%s.%s', $ymd, uniqid(), $file->getClientOriginalExtension());
            if (!$file->move($destPath, $filename)) {
                return $this->error('保存文件失败');
            }

            return $this->success(['path' => $request->getSchemeAndHttpHost() . '/images/' . $filename]);
        } catch (\Exception $ex) {
            return $this->error($ex->getMessage());
        }
    }

    // 上传base64图片
    public function base64(Request $request)
    {
        $imageBase64 = $request->post('images');
        list($extension, $base64Content) = explode(',', $imageBase64);
        preg_match('/data\:image\/(.*?);base64/', $extension, $match);
        if (!$match) {
            return $this->error('base64图片格式不正确');
        }

        $ymd      = date('Ymd');
        $destPath = $this->destPath('images/' . $ymd);
        $filename = sprintf('/%s.%s', uniqid(), $match[1]);
        file_put_contents($destPath . $filename, base64_decode($base64Content));
        return $this->success(['path' => $request->getSchemeAndHttpHost() . "/images/{$ymd}{$filename}"]);
    }

    private function destPath($path)
    {
        $destPath = public_path($path);
        if (!file_exists($path)) {
            mkdir($destPath, 0755, true);
        }

        return $destPath;
    }

    /**
     * 校验文件
     *
     * @param Request $request
     * @param string $fileKey
     * @param string $fileType
     * @return array|\Illuminate\Http\UploadedFile|null
     * @throws Exception
     */
    protected function checkImportFile(Request $request, $fileKey = 'import-file', $fileType = [])
    {
        if (!$request->hasFile($fileKey)) {
            throw new Exception('导入失败！文件不存在');
        }

        $file = $request->file($fileKey);
        if (!$file->isValid()) {
            throw new Exception($file->getErrorMessage());
        }

        if (!in_array(strtolower($file->getClientOriginalExtension()), $fileType)) {
            throw new Exception(sprintf('导入失败！仅支持 %s格式的文件', implode(',', $fileType)));
        }

        return $file;
    }

}
