CREATE TABLE journey
(
    id INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
    user_id INT(11) COMMENT '关联用户表，标识该条日志所属',
    title VARCHAR(200) NOT NULL COMMENT '日志简短描述',
    content TEXT COMMENT '日志详细描述',
    create_time DATETIME NOT NULL,
    update_time DATETIME,
    authority TINYINT(4) DEFAULT '1' COMMENT '权限 0:私有  1: 公开  默认1',
    count_like INT(11) DEFAULT '0' COMMENT '喜欢数（点赞数）',
    md_datas TEXT COMMENT '日志关联的资源数据;
[
{
 `type`://关联`md_typs`
 `url`://资源地址
 `tag`://标签信息(比如单个图片的描述)
},{}
]',
    md_types VARCHAR(30) COMMENT '日志关联的资源类型   1:图片 2:视频',
    CONSTRAINT journey___fk_user_id FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE CASCADE
);
CREATE INDEX journey___fk_user_id ON journey (user_id);
CREATE TABLE journey_comments
(
    id INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
    user_id INT(11) DEFAULT '0' NOT NULL COMMENT '评论者id',
    journey_id INT(11) DEFAULT '0' NOT NULL COMMENT '该评论所属日志id',
    parent_comment_id INT(11) DEFAULT '0' COMMENT '该评论所指向的上级评论',
    content TEXT NOT NULL COMMENT '评论内容',
    count_like INT(11) DEFAULT '0' COMMENT '喜欢数 点赞数等',
    count_unlike INT(11) DEFAULT '0' COMMENT '不喜欢数',
    create_time DATETIME COMMENT '该条消息记录时间(创建、评论)',
    CONSTRAINT journey_comments___fk_user_id FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE CASCADE,
    CONSTRAINT journey_comments___fk_journy_id FOREIGN KEY (journey_id) REFERENCES journey (id) ON UPDATE CASCADE
);
CREATE INDEX journey_comments___fk_journy_id ON journey_comments (journey_id);
CREATE INDEX journey_comments___fk_user_id ON journey_comments (user_id);
CREATE TABLE user
(
    id INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
    user_name VARCHAR(64) DEFAULT '' NOT NULL,
    nick_name VARCHAR(15) DEFAULT '' NOT NULL COMMENT '昵称',
    login_pswd VARCHAR(48) DEFAULT '' NOT NULL COMMENT '登陆密码',
    gender TINYINT(4) DEFAULT '-1' COMMENT '性别  0:女 1:男',
    avatar_url VARCHAR(256) COMMENT '头像地址',
    login_token VARCHAR(64) COMMENT '登陆token',
    push_token VARCHAR(64) COMMENT '推送token',
    register_time DATETIME COMMENT '注册时间',
    login_time_latest DATETIME COMMENT '最近登陆时间',
    count_concern INT(11) DEFAULT '0' COMMENT '被关注数量',
    user_status TINYINT(4) DEFAULT '0' COMMENT '用户状态',
    user_type TINYINT(4) DEFAULT '0' COMMENT '用户类型 -1:系统僵尸用户 0: 一般类型 1: 超级用户',
    user_source TINYINT(4) DEFAULT '0' COMMENT '用户来源（注册）0:unknow 1:android 2:ios 3:web',
    count_visitor_been INT(11) DEFAULT '0' COMMENT '被访问总量',
    mobile VARCHAR(11) COMMENT '用户手机号',
    login_status TINYINT(1) DEFAULT '0' COMMENT '是否为登陆状态'
);
CREATE TABLE user_concern
(
    id INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
    concern_user_id INT(11) DEFAULT '0' NOT NULL COMMENT '关注者user_id',
    concerned_user_id INT(11) DEFAULT '0' NOT NULL COMMENT '被关注者user_id',
    concern_time DATETIME COMMENT '关注时间',
    CONSTRAINT user_concern___fk_concern_user_id FOREIGN KEY (concern_user_id) REFERENCES user (id) ON UPDATE CASCADE,
    CONSTRAINT user_concern___fk_concerned_user_id FOREIGN KEY (concerned_user_id) REFERENCES user (id) ON UPDATE CASCADE
);
CREATE INDEX user_concern___fk_concerned_user_id ON user_concern (concerned_user_id);
CREATE INDEX user_concern___fk_concern_user_id ON user_concern (concern_user_id);