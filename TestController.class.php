<?php
namespace Admin\Controller;

use Think\Controller;

/**
 * 仅供开发使用
 */
class TestController extends Controller
{

    public function _initialize()
    {
    }


    public function index()
    {
        // https://10.5.6.99/security/generateYzm
        $id = 'generateYzm';
        $cachefile = '/mnt/NewSas/web/generateYzm.png';
        $config = array(
            'seKey' => 'ZfT6k4RUnXa3q', // 验证码加密密钥
            'fontSize' => 14,
            'imageH' => 30,
            'imageW' => 110,
            'length' => 4, // 验证码位数
            'anglerange' => array(-20, 20), // 角度变化范围
            'yrange' => array(20, 25), // 距离上方高度变化范围
            'xrange'   => array(6, 8), // 距离左方宽度变化范围
            // 复杂的
            'useCurve' => true, // 是否画混淆曲线
            'useNoise' => true, // 是否添加杂点
            'codeSet' => '356789bcfhkmnpuwxyABCEFGHJKLMNPRTUXY',
            // 'useZh'  => true, // 使用中文验证码
            'useNoiseLine' => true, // 是否使用产生干扰线
            'distortion' => true, // 是否扭曲文字
            'fontttf' => '4.ttf',
            
            // 简单的
            /* 'useCurve' => false, // 是否画混淆曲线
            'useNoise' => true, // 是否添加杂点
            'useNoiseLine' => false, // 是否使用产生干扰线
            'distortion' => false, // 是否扭曲文字
            'codeSet' => '367CEFGHJKLMNPRTUXY',
            'fontttf' => '4.ttf', */
        );
        $verify = new \Think\Verify($config);
        // $verify->entry();
        $verify->entry($id, true, $cachefile);
        echo json_encode([
            'img' => base64_encode(file_get_contents($cachefile)),
            'code' => session($id)
        ]);
    }
    
}
