<?php
/**
 * +----------------------------------------------------------------------
 * | wxpusher-sdk-php
 * +----------------------------------------------------------------------
 * | Copyright (c) 2020 Meloncn All rights reserved.
 * +----------------------------------------------------------------------
 * | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * +----------------------------------------------------------------------
 * | Github：  https://github.com/wxpusher/wxpusher-sdk-php
 * +----------------------------------------------------------------------
 * | Version： v1.0
 * +----------------------------------------------------------------------
 * | LastUpdate： 2020-03-17
 * +----------------------------------------------------------------------
 *
 */
namespace notify\wxpusher;

class Wxpusher
{
    protected $appToken;
    protected $appServer;
    protected $appMsgCheckGate;
    protected $appUserFunGate;

    function __construct(){

        $this->appToken = 'AT_xxx'; // Token在此定义或者引用

        $this->appMsgGate = 'http://wxpusher.zjiecode.com/api/send/message';
        $this->appMsgCheckGate = 'http://wxpusher.zjiecode.com/api/send/query';
        $this->appUserFunGate = 'http://wxpusher.zjiecode.com/api/fun/wxuser';
    }

    /**
     * @param $url
     * @param $jsonStr
     * @return array
     *  tools
     *  用于向服务器发送数据
     * 返回状态码与服务器信息
     *
     */
    function post_json($url, $jsonStr)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=utf-8',
            'Content-Length: ' . strlen($jsonStr)
        ));
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return  $response;
    }


    /**
     * 快速发送文本信息
     * 使用方法：
     *      $wx->quickSend('用户id','主题id','内容','http://domain');
     *          成功返回true
     *          失败返回错误信息
     *  用户ID与主题ID使用哪一个填写哪一个，不使用留空
     */
    public function quickSend($uid = null , $topicId = null , $content = 'Hello',$url = null){
        $data = http_build_query(
            array(
                'appToken' => $this->appToken,
                'content' => urlencode($content),
                'uid' => $uid,
                'topicId' => $topicId,
                'url'   => urlencode($url),
            ));
          $result = json_decode(file_get_contents($this->appMsgGate.'/?'.$data));
          if ($result->success){
              return true;
          }else{
              return $result;
          }
    }

    /**
     * @param null $content
     * @param int $contentType
     * @param bool $isUids
     * @param array int $id
     * @param string $url
     * @return string
     *
     * 标准信息发送方式
     *
     * $content: 您要发送的内容 \n换行
     *
     * $contentType:
     *      1表示文字
     *      2表示html
     *      3表示markdown
     *
     * $isUids:
     *      true    发送到用户
     *      false   发送到主题
     *
     * $url 需要添加协议头 http://或https://
     *
     * $getMessageId:
     *      true    执行完毕后返回messageId和错误信息   array
     *      false   执行完毕后仅返回错误信息，若无错误返回TRUE array bool
     *
     * $array_id:  单条可使用int类型，多条使用数组方式
     *      array
     *      int
     *
     * 使用方法实例：
     *      $wx->send('内容','类型','是否为用户id',id或数组id,'需传送的url',是否返回messageID))
     *
     */
    public function send($content = null,$contentType = 1,$isUids = true,$array_id = [],$url = '',$getMessageId = false)
    {
        {
            $type = $isUids?'uids':'topicIds';

            //记录错误信息
            $error = [];

            // 若 $array_id 直接输入int则此处进行转换
            if (!is_array($array_id)){
                $array_id = ["$array_id"];
            }
            $postdata = array(
                    'appToken' => $this->appToken,
                    'content' => $content,
                    $type   => $array_id,
                    'url' => $url,
                );
            $jsonStr = json_encode($postdata);
            $result = json_decode($this->post_json($this->appMsgGate, $jsonStr),TRUE)['data'];//取出data内执行信息
            foreach ($result as $key => $k){
               if ($k['code'] !== 1000){
                   $error[] = $k;
               }
            }

            //记录发送成功MessageID
            if ($getMessageId){
                foreach ($result as $key => $k){
                    if ($k['code'] == 1000){
                        $messageId[] = $k['messageId'];
                    }
                }
                if (empty($messageId)){
                    $messageId = [];
                }
            }
            if (empty($error)){ // 没有出现错误
                if ($getMessageId){ //判断是否需要输出if
                    return $messageId;  //输出messageID
                }else{
                    return true;   // 不需要输出MessageID直接True
                }
            }else{                 //出错状态
                if ($getMessageId){ //判断是否需要输出Id
                    return array('error' => $error,'messageId' => $messageId);    //输出错误信息和ID
                }else{
                    return $error;  //仅输出错误
                }
                }
        }
    }

    /**
     * @param $messageId
     * @return bool
     *  信息查询状态
     *  信息发送成功返回 True
     *  其余状态返回服务器提示信息(msg)
     */
    public function checkStatus($messageId){
        $result = json_decode(file_get_contents($this->appMsgCheckGate.'/'.$messageId));
        if ($result->code == 1000){
            return true;
        }else{
            return $result->msg;
        }
    }

    /**
     * @param int $page
     * @param int $pageSize
     * @param string $uid
     * @return null|string
     *
     * 获取关注用户信息
     *  $uid 默认为空，返回所有关注用户数据    多维数组
     *       输入uid，输出指定用户信息        多维数组
     *
     * 远程服务器执行返回success继续执行
     *  否则返回远程服务器错误信息
     *
     * 查询数据
     *      得到数据返回多维数组
     *      空数据返回 NULL
     */

    public function getFunInfo($page = 1,$pageSize = 100,$uid = ''){
        $data = http_build_query(
            array(
                'appToken' => $this->appToken,
                'page' => $page,
                'pageSize' => $pageSize,
                'uid'   => $uid
            ));
        $result = json_decode($result = file_get_contents($this->appUserFunGate.'/?'.$data),true);
        if ($result['code'] == 1000){ //判断服务器是否执行成功
            $data = $result['data']['records'];
            if (empty($data)){
                return null;
            }else{
                return $data;
            }
        }else{
            return $result['msg']; //反馈服务器给出的错误信息
        }
    }

    /**
     * @return mixed
     *  返回用户关注总数 int
     */
    public function getFunTotal(){
        $data = http_build_query(
            array(
                'appToken' => $this->appToken,
                'page' => 1,
                'pageSize' => 1,
            ));
        $result = json_decode($result = file_get_contents($this->appUserFunGate.'/?'.$data),true);
        if ($result['code'] == 1000){ //判断服务器是否执行成功
            return $result['data']['total'];
        }else{
            return $result['msg']; //反馈服务器给出的错误信息
        }
    }
}