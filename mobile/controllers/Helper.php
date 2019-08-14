<?php
/**
 * User: Colin
 * Time: 2019/3/14 16:41
 */

namespace mobile\controllers;


class Helper
{
    /**
     * 发送 POST 请求
     * @param $url
     * @param $postData
     * @return bool|string
     */
    public static function sendPost($url, $postData) {
        $postData = http_build_query($postData);
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-type:application/x-www-form-urlencoded',
                'content' => $postData,
                'timeout' => 15 * 60 // 超时时间（单位:s）
            )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        return $result;
    }

    /**
     * curl 请求
     * @param $url
     * @param int $isPost
     * @param string $dataFields
     * @param string $cookieFile
     * @param bool $v
     * @return mixed
     */
    public static function curl($url, $isPost = 0, $dataFields = '', $cookieFile = '', $v = false) {
        $header = array("Connection: Keep-Alive","Accept: text/html, application/xhtml+xml, */*",
            "Pragma: no-cache", "Accept-Language: zh-Hans-CN,zh-Hans;q=0.8,en-US;q=0.5,en;q=0.3","User-Agent: Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.2; WOW64; Trident/6.0)");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, $v);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $isPost && curl_setopt($ch, CURLOPT_POST, $isPost);
        $isPost && curl_setopt($ch, CURLOPT_POSTFIELDS, $dataFields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
        $cookieFile && curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
        $cookieFile && curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
        $r = curl_exec($ch);
        curl_close($ch);
        return $r;
    }

    /**
     * 人性化显示时间
     * @param $time
     * @return false|string
     */
    public static function HumanTime($time)
    {
        $formatTime = date("m-d H:i", $time);
        $newTime = time() - $time;
        if ($newTime < 60) {
            $str = '刚刚';
        } elseif ($newTime < 60 * 60) {
            $min = floor($newTime / 60);
            $str = $min . '分钟前';
        } elseif ($newTime < 60 * 60 * 24) {
            $h = floor($newTime / (60 * 60));
            $str = $h . '小时前';
        } elseif ($newTime < 60 * 60 * 24 * 3) {
            $d = floor($newTime / (60 * 60 * 24));
            if ($d == 1) {
                $str = '昨天 ' . $formatTime;
            } else {
                $str = '前天 ' . $formatTime;
            }
        } else {
            $str = $formatTime;
        }
        return $str;
    }

    /**
     * 匹配字符串URL并替换为超链接
     * @param $str
     * @return mixed|string
     */
    public static function formatUrlsInText($str)
    {
        $pattern = '/((http|ftp|https):\/\/)?([\w_-]+(?:(?:\.[\w_-]+)+))([\w.,@?^=%&:\/~+#-]*[\w@?^=%&\/~+#-])?/';
        preg_match_all($pattern, $str, $arr);
        if (!$arr[0]) {
            return $str;
        }
        $replaceOnce = function ($needle, $replace, $haystack) {
            $pos = strpos($haystack, $needle);
            if ($pos === false) {
                return $haystack;
            }
            return substr_replace($haystack, $replace, $pos, strlen($needle));
        };
        $_tmp = [];
        foreach ($arr[0] as $v) {
            $tmp = explode($v, $str);
            $_tmp[] = $tmp[0];
            $_tmp[] = "<a href='{$v}' target='_blank' style='color: rgb(48, 151, 253)'>{$v}</a>";
            $str = $replaceOnce($tmp[0] . $v, '', $str);
        }
        return join($_tmp, ' ');
    }

}
