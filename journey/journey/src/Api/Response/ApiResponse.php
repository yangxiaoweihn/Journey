<?php

namespace Journey\Api\Response;
/**
 * Created by PhpStorm.
 * User: yangxiaowei
 * Date: 16/11/10
 * Time: 17:36
 *
 * 接口数据返回
 */
class ApiResponse{

    /**
     * @param $code
     * @param string $msg
     * @param array $data
     * @param string $format
     */
    public static function ResponseWith($code, $msg = '', $data = array(), $format = 'json') {
        $format = strtolower($format);
        if ($format == 'json') {
            self::JSON($code, $msg, $data);
        }elseif ($format == 'xml') {
            self::XML($code, $msg, $data);
        }
    }

    /**
     * json数据
     * @param $code
     * @param string $msg
     * @param array $data
     * @return string
     */
    private static function JSON($code, $msg = '', $data = array()) {
        if (!is_numeric($code)) {
            return '';
        }

        $result = self::responseFormat($code, $msg, $data);

        echo json_encode($result);
    }

    /**
     * xml数据
     * @param $code
     * @param string $msg
     * @param array $data
     * @return string
     */
    private static function XML($code, $msg = '', $data = array()) {
        if (!is_numeric($code)) {
            return '';
        }

        $result = self::responseFormat($code, $msg, $data);

        header("Content-Type:text/xml");
        $xml = "<?xml version='1.0' encoding='UTF-8' ?>\n";
        $xml .= "<root>\n";
        $xml .= self::dataToXml($result);
        $xml .= "</root>";

        echo $xml;
    }

    /**
     * @param array $data
     * @return string
     */
    private static function dataToXml(array $data) {
        $xml = "";
        foreach ($data as $key => $value) {
            $attr = "";
            if (is_numeric($key)) {
                $attr = " id= '{$key}'";
                $key = "item";
            }
            $xml .= "<{$key}{$attr}>";
            $xml .= is_array($value) ? self::dataToXml($value) : $value;
            $xml .= "</{$key}>\n";
        }
        return $xml;
    }

    private static function responseFormat($code, $msg = '', $data = array()) {
        return array(
            'code' => $code,
            'message' => $msg,
            'data' => empty($data) ? null : $data
        );
    }
}