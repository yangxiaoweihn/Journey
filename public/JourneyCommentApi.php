<?php
use Slim\Http\Request as SlimRequest;
use Slim\Http\Response as SlimResponse;
use Journey\Api\Response\ApiResponse as ApiResponse;
use Journey\Api\Common\ErrorCode as ErrorCode;
use Journey\Api\Journey\JourneyCommentsImpl as JourneyCommentsImpl;
use \Journey\Api\Utils\StringUtils as StringUtils;
/**
 * Created by PhpStorm.
 * User: yangxiaowei
 * Date: 16/11/15
 * Time: 15:27
 */
class JourneyCommentApi extends BaseApi {
    public function __construct(Slim\App $app){
        parent::__construct($app);
        $this->journeyComment();
        $this->getJourneyCommentsList();
    }

    /**
     * POST
     *
     * 日志评论、评论评论
     *
     * @path /journey/comment/commitComment
     * @param [require] userId 评论者id
     * @param [require] journeyId
     * @param [require] content
     * @param [option]  toCommentId 针对哪条评论
     *
     */
    private function journeyComment() {
        $this->app->post('/journey/comment/commitComment', function(SlimRequest $request, SlimResponse $response) {
            $userId = $request->getParam('userId');

            if (!self::checkUserId($userId)) {
                return ;
            }
            $journeyId = $request->getParam('journeyId');
            $content = $request->getParam('content');

            if (!is_numeric($journeyId) || StringUtils::isEmpty($content)) {
                $code = ErrorCode::ER_PARAM_TYPE_DIS;
                ApiResponse::ResponseWith($code, ErrorCode::$ER_ALL[$code]);
                return ;
            }

            $parentCommentId = self::checkIntParamAndSetDefault($request->getParam('toCommentId'), 0);

            $journeyComment = new JourneyCommentsImpl();
            $entity = $journeyComment->commentJourney($userId, $journeyId, $parentCommentId, $content);

            if (null == $entity) {
                $code = ErrorCode::ER_SERVER_OPERATE;
                ApiResponse::ResponseWith($code, ErrorCode::$ER_ALL[$code]);
                return ;
            }

            $data = array(
                'id'=>$entity->id,
                'journeyId'=>$entity->journeyId,
                'parentCommentId'=>$entity->parentCommentId,
                'createTime'=>$entity->createTime
            );

            ApiResponse::ResponseWith(ErrorCode::ER_SUCCESS, '评论成功', $data);

        });
    }

    /**
     * GET
     *
     * 获取日志评论列表[支持分页]
     *
     * @path /journey/comment/getJourneyCommentsList
     * @param [require]
     * @param [option] Common::PARAM_PAGEOFFSET
     * @param [option] Common::PARAM_PAGESIZE
     */
    private function getJourneyCommentsList() {
        $this->app->get('/journey/comment/getJourneyCommentsList', function(SlimRequest $request) {
            $journeyId = self::checkIntParamAndSetDefault($request->getParam('journeyId'));

            if ($journeyId <= 0) {
                $code = ErrorCode::ER_PARAM_TYPE_DIS;
                ApiResponse::ResponseWith($code, ErrorCode::$ER_ALL[$code]);
                return ;
            }

            $pageOffset = self::checkIntParamAndSetDefault($request->getParam(Common::PARAM_PAGEOFFSET));
            $pageSize = self::checkIntParamAndSetDefault($request->getParam(Common::PARAM_PAGESIZE));
            $journeyComment = new JourneyCommentsImpl();
            $comments = $journeyComment->getJourneyCommentsList($journeyId, $pageOffset, $pageSize);
            ApiResponse::ResponseWith(0, '查询成功', $comments);
        });
    }


    /**
     *
     */
    private function doLikeComment() {

    }

}