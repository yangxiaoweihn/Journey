<?php
use Slim\Http\Request as SlimRequest;
use Slim\Http\Response as SlimResponse;
use Journey\Api\Response\ApiResponse as ApiResponse;
use Journey\Api\Journey\JourneyImpl as JourneyImpl;
/**
 * Created by PhpStorm.
 * User: yangxiaowei
 * Date: 16/11/15
 * Time: 15:27
 */
class JourneyApi extends BaseApi {
    public function __construct(Slim\App $app){
        $this->journeyAdd($app);
        $this->journeyGet($app);
    }


    private function journeyAdd(Slim\App $app) {
        /**
         * 每个文件的key规则
         * 文件信息key{fileKey}: fileXX
         * 文件简短描述信息key{fileDescKey}: {fileKey}Desc
         * 比如： file1 -> file1Desc || fileImg -> fileImgDesc
         */
        $app->post('/journey/add', function(SlimRequest $request, SlimResponse $response) {
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

    private function journeyGet(Slim\App $app) {
        /**
         * 获取旅途日志数据
         */
        $app->get('/journey/get', function (\Slim\Http\Request $request, \Slim\Http\Response $response, $args) {
            if (false == $this->filterHeader($request)) {
                return;
            }

            $journeyImpl = new JourneyImpl();
            $datas = $journeyImpl->getJourneyBy($args['userId'], 0, 0);
            ApiResponse::ResponseWith(0, '获取成功', $datas);
        });
    }
}