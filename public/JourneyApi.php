<?php
use Slim\Http\Request as SlimRequest;
use Slim\Http\Response as SlimResponse;
use Journey\Api\Response\ApiResponse as ApiResponse;
use Journey\Api\Journey\JourneyImpl as JourneyImpl;
use Journey\Api\Common\ErrorCode as ErrorCode;
/**
 * Created by PhpStorm.
 * User: yangxiaowei
 * Date: 16/11/15
 * Time: 15:27
 */
class JourneyApi extends BaseApi {
    public function __construct(Slim\App $app){
        parent::__construct($app);
        $this->journeyAdd();
        $this->journeyGet();
        $this->journeyLikeBy();
    }


    private function journeyAdd() {
        /**
         * 每个文件的key规则
         * 文件信息key{fileKey}: fileXX
         * 文件简短描述信息key{fileDescKey}: {fileKey}Desc
         * 比如： file1 -> file1Desc || fileImg -> fileImgDesc
         */
        $this->app->post('/journey/add', function(SlimRequest $request, SlimResponse $response) {
            $files = $request->getUploadedFiles();
            $params = $request->getParams();

//    var_dump($files);
//
//    var_dump($params);

            //存放上传素材信息
            $mdInfos = array();

            $imgTag = '';
            if (!empty($files)) {
                foreach ($files as $key => $value) {
                    //存放单个素材信息
                    $mdInfo = array();
//            echo $key.' -- '.$value->{'file'};
                    $fileSave = __DIR__.'/../journey/A/'.$key;
                    rename($value->{'file'}, $fileSave);
                    $imgTag .= '<img width="200px" height="200px" src="'.$fileSave.'" />';

                    $mdInfo['url'] = $fileSave;
                    if (!empty($params)) {
                        $suffix = 'Desc';
                        $desc = $params[$key.$suffix];
                        if (null != $desc) {
                            $mdInfo['tag'] = $desc;
                        }
                    }

                    array_push($mdInfos, $mdInfo);
                }
            }

//    var_dump($mdInfos);
//    echo '<br/>'.json_encode($mdInfos).'<br/>';


            $journeyImp = new JourneyImpl();
            $userId = null == $params['userId'] ? 0 : $params['userId'];
            $authority = null == $params['authority'] ? 1 : $params['authority'];
            $rel = $journeyImp->addJourney((int) $userId, $params['title'], $params['content'], (int) $authority, $mdInfos);


//    echo '</br>====='.$rel.'</br>';

            ApiResponse::ResponseWith($rel['code'], $rel['msg']);

            //test code
            $img = $request->getBody()->getMetadata('file1');

            $file_img = __DIR__.'/../journey/A/shit.jpeg';
            file_put_contents($file_img, $img);

            $con_html = '<!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <title>Title</title>
            </head>
            <body>
            '.$imgTag.'
            </body>
            </html>';
            $file_html = __DIR__.'/../journey/A/shit.html';
            file_put_contents($file_html, $con_html);
        });
    }

    private function journeyGet() {
        /**
         * 获取旅途日志数据
         */
        $this->app->get('/journey/get', function (SlimRequest $request, SlimResponse $response, $args) {
            if (false == self::filterHeader($request)) {
                return;
            }

            $journeyImpl = new JourneyImpl();
            $datas = $journeyImpl->getJourneyBy($args['userId'], $args[Param_PageOffset], $args[Param_PageSize]);
            ApiResponse::ResponseWith(0, '获取成功', $datas);
        });
    }

    private function journeyLikeBy() {
        /**
         * 喜欢旅途日志
         */
        $this->app->put('/journey/like', function (SlimRequest $request, SlimResponse $response, $args) {
            if (false == self::filterHeader($request)) {
                return;
            }
            $journeyImpl = new JourneyImpl();
            $rel = $journeyImpl->likeJourneyBy($request->getParam('journeyId'));
            ApiResponse::ResponseWith($rel, ErrorCode::ER_SUCCESS == $rel ? '操作成功' : ErrorCode::$ER_ALL[$rel], null);
        });
    }
}