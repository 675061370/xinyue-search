/*
 Navicat Premium Data Transfer

 Source Server         : cms本地
 Source Server Type    : MySQL
 Source Server Version : 50726
 Source Host           : localhost:3306
 Source Schema         : www_dj_com

 Target Server Type    : MySQL
 Target Server Version : 50726
 File Encoding         : 65001

 Date: 14/09/2024 16:54:39
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for qf_access
-- ----------------------------
DROP TABLE IF EXISTS `qf_access`;
CREATE TABLE `qf_access`  (
  `access_id` int(11) NOT NULL AUTO_INCREMENT,
  `access_admin` int(11) NOT NULL DEFAULT 0 COMMENT '用户ID',
  `access_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'AccessToken',
  `access_plat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'all' COMMENT '登录平台',
  `access_ip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'IP',
  `access_status` int(11) NOT NULL DEFAULT 0 COMMENT '状态',
  `access_createtime` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `access_updatetime` int(11) NOT NULL DEFAULT 0 COMMENT '修改时间',
  PRIMARY KEY (`access_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '授权信息表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of qf_access
-- ----------------------------
INSERT INTO `qf_access` VALUES (1, 1, 'e87d3ab11608533d4ee0cb05a21aee6f8e44865c100000e87d3ab11608533d4ee0cb05a21aee6f8e44865c', 'admin', '127.0.0.1', 0, 1726298717, 1726303168);

-- ----------------------------
-- Table structure for qf_admin
-- ----------------------------
DROP TABLE IF EXISTS `qf_admin`;
CREATE TABLE `qf_admin`  (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'UID',
  `admin_account` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '帐号',
  `admin_password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '密码',
  `admin_salt` varchar(4) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '密码盐',
  `admin_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '用户昵称',
  `admin_idcard` varchar(18) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '身份证',
  `admin_truename` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '真实姓名',
  `admin_email` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '邮箱',
  `admin_money` decimal(9, 2) NOT NULL DEFAULT 0.00 COMMENT '余额',
  `admin_group` int(11) NOT NULL DEFAULT 0 COMMENT '用户组',
  `admin_ipreg` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '注册IP',
  `admin_status` int(11) NOT NULL DEFAULT 0 COMMENT '1被禁用',
  `admin_createtime` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `admin_updatetime` int(11) NOT NULL DEFAULT 0 COMMENT '修改时间',
  PRIMARY KEY (`admin_id`) USING BTREE,
  INDEX `admin_group`(`admin_group`) USING BTREE,
  INDEX `admin_name`(`admin_name`) USING BTREE,
  INDEX `admin_password`(`admin_password`) USING BTREE,
  INDEX `admin_account`(`admin_account`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of qf_admin
-- ----------------------------
INSERT INTO `qf_admin` VALUES (1, 'admin', 'b806edc1a9e170c683c73e9ea486bbc9ffc07eb5', 'BuVf', '超级管理员', '', '超级管理员', '', 0.00, 1, '127.0.0.1', 0, 0, 1726303168);

-- ----------------------------
-- Table structure for qf_attach
-- ----------------------------
DROP TABLE IF EXISTS `qf_attach`;
CREATE TABLE `qf_attach`  (
  `attach_id` int(11) NOT NULL AUTO_INCREMENT,
  `attach_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '文件名',
  `attach_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '路径',
  `attach_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '类型',
  `attach_size` int(11) NOT NULL DEFAULT 0 COMMENT '大小',
  `attach_admin` int(11) NOT NULL DEFAULT 0 COMMENT '用户',
  `attach_status` int(11) NOT NULL DEFAULT 0 COMMENT '状态',
  `attach_createtime` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `attach_updatetime` int(11) NOT NULL DEFAULT 0 COMMENT '修改时间',
  PRIMARY KEY (`attach_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '附件表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for qf_auth
-- ----------------------------
DROP TABLE IF EXISTS `qf_auth`;
CREATE TABLE `qf_auth`  (
  `auth_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '权限ID',
  `auth_group` int(11) NOT NULL DEFAULT 0 COMMENT '权限管理组',
  `auth_node` int(11) NOT NULL DEFAULT 0 COMMENT '功能ID',
  `auth_status` int(11) NOT NULL DEFAULT 0 COMMENT '1被禁用',
  `auth_createtime` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `auth_updatetime` int(11) NOT NULL DEFAULT 0 COMMENT '修改时间',
  PRIMARY KEY (`auth_id`) USING BTREE,
  INDEX `role_group`(`auth_group`) USING BTREE,
  INDEX `role_auth`(`auth_node`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '权限表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for qf_conf
-- ----------------------------
DROP TABLE IF EXISTS `qf_conf`;
CREATE TABLE `qf_conf`  (
  `conf_id` int(11) NOT NULL AUTO_INCREMENT,
  `conf_key` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '参数名',
  `conf_value` text CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT '参数值',
  `conf_title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT '参数名称',
  `conf_desc` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT '参数描述',
  `conf_int` int(11) NOT NULL DEFAULT 0 COMMENT '参数到期',
  `conf_spec` int(11) NOT NULL DEFAULT 0 COMMENT '文本类型',
  `conf_content` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '单选多选等文本类型的数据集',
  `conf_type` int(11) NOT NULL DEFAULT 0 COMMENT '配置分类 ',
  `conf_status` int(11) NOT NULL DEFAULT 0 COMMENT '显示隐藏 0是隐藏',
  `conf_sort` int(11) NOT NULL DEFAULT 0 COMMENT '排序',
  `conf_system` int(11) NOT NULL DEFAULT 0 COMMENT '1为系统参数，请勿删除',
  `conf_createtime` int(11) NOT NULL DEFAULT 0,
  `conf_updatetime` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`conf_id`) USING BTREE,
  INDEX `conf_key`(`conf_key`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 52 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '配置表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of qf_conf
-- ----------------------------
INSERT INTO `qf_conf` VALUES (1, 'app_name', '资源管理系统', '网站名称', '', 0, 0, NULL, 0, 1, 99, 1, 0, 1725411498);
INSERT INTO `qf_conf` VALUES (2, 'upload_max_file', '4097152', '最大文件上传限制', '', 0, 0, NULL, 2, 1, 0, 1, 0, 1617352067);
INSERT INTO `qf_conf` VALUES (3, 'upload_file_type', 'csv,xlsx', '允许文件上传类型', '', 0, 0, NULL, 2, 1, 0, 1, 0, 1617351959);
INSERT INTO `qf_conf` VALUES (4, 'upload_max_image', '2097152', '最大图片上传限制', '', 0, 0, NULL, 2, 1, 0, 1, 0, 1617351961);
INSERT INTO `qf_conf` VALUES (5, 'upload_image_type', 'jpg,png,gif,jpeg,bmp', '允许上传图片类型', '', 0, 0, NULL, 2, 1, 0, 1, 0, 1617351964);
INSERT INTO `qf_conf` VALUES (21, 'logo', '', '网站LOGO', '方形LOGO，最佳显示尺寸为80*80像素', 0, 4, NULL, 0, 1, 93, 1, 1711636952, 1725006864);
INSERT INTO `qf_conf` VALUES (22, 'quark_cookie', '', '夸克Cookie', '', 0, 0, NULL, 4, 1, 0, 1, 1712114435, 1712114652);
INSERT INTO `qf_conf` VALUES (23, 'qcode', '', '群二维码', '前台加入群聊开关；有图显示按钮，无图不显示', 0, 4, NULL, 3, 1, 80, 1, 1712451616, 1725326400);
INSERT INTO `qf_conf` VALUES (24, 'app_description', '', 'SEO描述', '', 0, 1, NULL, 9, 1, 996, 1, 1712451778, 1725411481);
INSERT INTO `qf_conf` VALUES (25, 'quark_banned', '失效,年会员,空间容量,微信,微信群,全网资源,影视资源,扫码,最新资源,公众号,IMG_,资源汇总,緑铯粢源,.url,网盘推广,大额优惠券,资源文档,dy8.xyz,妙妙屋,资源合集,kkdm', '广告词', '出现这些词的资源，转存时删除；格式如：影视资源,年会员', 0, 1, NULL, 4, 1, 0, 1, 1714035639, 1723795683);
INSERT INTO `qf_conf` VALUES (26, 'Authorization', '', '阿里Authorization', '此版本用不着', 0, 0, NULL, 4, 1, 0, 1, 1722010465, 1722010465);
INSERT INTO `qf_conf` VALUES (27, 'mp4_online', '0', '在线观看资源', '此版本用不着', 0, 2, '开启=>1\n关闭=>0', 4, 1, 0, 1, 1723014926, 1723014926);
INSERT INTO `qf_conf` VALUES (28, 'search_type', '1', '搜索模式', '精准搜索：只有查包含关键词的；模糊搜索：关键词顺序可乱但必须都包含；分词搜索：只要满足其中一个字就会搜索到', 0, 2, '精准搜索=>0\n模糊搜索=>1\n分词搜索=>2', 1, 1, 0, 1, 1724493746, 1724494058);
INSERT INTO `qf_conf` VALUES (29, 'app_keywords', 'XXX,XXX,XXXX', 'SEO关键词', '网站关键词，有利于对整站的SEO优化', 0, 1, NULL, 9, 1, 998, 1, 1725006403, 1725411476);
INSERT INTO `qf_conf` VALUES (30, 'app_title', 'XXXXXXXXXXXX', 'SEO标题', '', 0, 0, NULL, 9, 1, 999, 1, 1725006679, 1725325013);
INSERT INTO `qf_conf` VALUES (31, 'app_subname', 'Hello World', '网站宣传语', '免费分享百万级网盘资源，致力打造顶尖网盘搜索引擎，让您畅享资源无忧！', 0, 0, NULL, 0, 1, 94, 1, 1725006792, 1725006869);
INSERT INTO `qf_conf` VALUES (32, 'home_bg', '', '大图背景', '', 0, 4, '', 3, 1, 75, 1, 1725007588, 1725007613);
INSERT INTO `qf_conf` VALUES (33, 'home_background', NULL, '背景颜色', '默认：#fafafa', 0, 7, NULL, 3, 1, 74, 1, 1725007770, 1725027349);
INSERT INTO `qf_conf` VALUES (34, 'footer_dec', '声明：本站是网盘索引系统,所有内容均来自互联网所提供的公开引用资源，未提供资源上传、存储服务。', '底部介绍', '示例：声明：本站是网盘索引系统,所有内容均来自互联网所提供的公开引用资源，未提供资源上传、存储服务。', 0, 1, NULL, 0, 1, 90, 1, 1725025185, 1725325534);
INSERT INTO `qf_conf` VALUES (35, 'footer_copyright', '© 2024 心悦 Powered by <a href=\"https://github.com/675061370/xinyue-search/\" target=\"_blank\">心悦</a>', '底部版权', '示例：© 2024 心悦 Powered by <a href=\"https://github.com/675061370/xinyue-search/\" target=\"_blank\">心悦</a>', 0, 1, NULL, 0, 1, 89, 1, 1725025262, 1725325624);
INSERT INTO `qf_conf` VALUES (36, 'home_color', NULL, '文字颜色', '默认文字颜色：#000000', 0, 7, NULL, 3, 1, 73, 1, 1725027432, 1725027445);
INSERT INTO `qf_conf` VALUES (37, 'home_theme', NULL, '主题色', '默认：#1e80ff', 0, 7, NULL, 3, 1, 72, 1, 1725027499, 1725027504);
INSERT INTO `qf_conf` VALUES (38, 'other_background', NULL, '其它元素背景', '搜索框及其它元素北背景色 默认：#ffffff', 0, 7, NULL, 3, 1, 71, 1, 1725028468, 1725028478);
INSERT INTO `qf_conf` VALUES (39, 'ranking_type', '0', '显示模式', '', 0, 2, '无图模式=>0\n有图模式=>1', 3, 1, 79, 1, 1725159933, 1725160022);
INSERT INTO `qf_conf` VALUES (40, 'ranking_num', '10', '排行榜数量', '下次更新生效；排行榜数据每12个小时更新一次；右上角清除缓存立即生效', 0, 0, NULL, 3, 1, 78, 1, 1725160003, 1725171288);
INSERT INTO `qf_conf` VALUES (41, 'home_css', '', '自定义CSS', '直接写css样式就行', 0, 1, NULL, 3, 1, 70, 1, 1725324697, 1725324697);
INSERT INTO `qf_conf` VALUES (42, 'seo_statistics', '', '统计代码', '直接填写统计代码即可，如51LA： <script charset=\"UTF-8\" id=\"XXXXX\" src=\"//sdk.51.la/js-sdk-pro.min.js\"></script> 	<script>LA.init({id:\"XXXXX\",ck:\"XXXX\",hashMode:true})</script>', 0, 1, NULL, 9, 1, 995, 1, 1725325341, 1725411486);
INSERT INTO `qf_conf` VALUES (43, 'app_icon', '', '网站icon', '', 0, 4, NULL, 0, 1, 92, 1, 1725326071, 1725326071);
INSERT INTO `qf_conf` VALUES (44, 'app_demand', '0', '提交需求', '前台是否开启此功能 ；  默认开启', 0, 2, '开启=>0\n关闭=>1', 3, 1, 81, 1, 1725326640, 1725326707);
INSERT INTO `qf_conf` VALUES (45, 'app_links', '<a href=\"https://github.com/675061370/xinyue-search/\" target=\"_blank\">更多资源</a>', '顶部其他外链', '一行一个外链(a标签)：<a href=\"https://github.com/675061370/xinyue-search/\" target=\"_blank\">更多资源</a>', 0, 1, NULL, 3, 1, 80, 1, 1725326838, 1725326838);
INSERT INTO `qf_conf` VALUES (46, 'app_name_hide', '0', '隐藏网站名称', '默认显示：logo包含文字的可以隐藏网站名称', 0, 2, '显示=>0\n隐藏=>1', 0, 1, 98, 1, 1725411632, 1725411763);
INSERT INTO `qf_conf` VALUES (47, 'ranking_m_num', '6', '移动端限制数量', '释：移动端最多显示数量', 0, 0, NULL, 3, 1, 77, 1, 1725412329, 1725412329);
INSERT INTO `qf_conf` VALUES (48, 'search_tips', '', '未搜索提示词', '为空时默认：未找到，可换个关键词尝试哦~', 0, 0, NULL, 1, 1, 0, 1, 1726108804, 1726108804);
INSERT INTO `qf_conf` VALUES (49, 'search_bg', '', '未搜索提示图', '', 0, 4, NULL, 1, 1, 0, 1, 1726108851, 1726108851);
INSERT INTO `qf_conf` VALUES (50, 'home_new', '1', '最新列表', '仅无图模式有效', 0, 2, '开启=>0\n关闭=>1', 3, 1, 79, 1, 1726299605, 1726299605);
INSERT INTO `qf_conf` VALUES (51, 'home_new_img', '', '最新图标', '', 0, 4, NULL, 3, 1, 79, 1, 1726302688, 1726302688);
INSERT INTO `qf_conf` VALUES (52, 'is_quan', '0', '全网搜', '', 0, 2, '关闭=>0\n开启=>1', 1, 1, 1, 1, 1729928547, 1729928547);


-- ----------------------------
-- Table structure for qf_days
-- ----------------------------
DROP TABLE IF EXISTS `qf_days`;
CREATE TABLE `qf_days`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '',
  `time` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for qf_feedback
-- ----------------------------
DROP TABLE IF EXISTS `qf_feedback`;
CREATE TABLE `qf_feedback`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `create_time` int(11) NOT NULL DEFAULT 0,
  `update_time` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for qf_group
-- ----------------------------
DROP TABLE IF EXISTS `qf_group`;
CREATE TABLE `qf_group`  (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '管理组名称',
  `group_desc` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '管理组描述',
  `group_status` int(11) NOT NULL DEFAULT 0 COMMENT '1被禁用',
  `group_createtime` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `group_updatetime` int(11) NOT NULL DEFAULT 0 COMMENT '修改时间',
  PRIMARY KEY (`group_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '管理组表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of qf_group
-- ----------------------------
INSERT INTO `qf_group` VALUES (1, '超级管理员', '不允许删除', 0, 0, 1575903468);

-- ----------------------------
-- Table structure for qf_log
-- ----------------------------
DROP TABLE IF EXISTS `qf_log`;
CREATE TABLE `qf_log`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `domain` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `update_time` int(11) NOT NULL DEFAULT 0,
  `create_time` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for qf_node
-- ----------------------------
DROP TABLE IF EXISTS `qf_node`;
CREATE TABLE `qf_node`  (
  `node_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '功能ID',
  `node_title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '功能名称',
  `node_desc` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '功能描述',
  `node_module` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'api' COMMENT '模块',
  `node_controller` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '控制器',
  `node_action` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '方法',
  `node_pid` int(11) NOT NULL DEFAULT 0 COMMENT '父ID',
  `node_order` int(11) NOT NULL DEFAULT 0 COMMENT '排序ID',
  `node_show` int(11) NOT NULL DEFAULT 1 COMMENT '1显示到菜单',
  `node_icon` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '图标',
  `node_extend` text CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT '扩展数据',
  `node_status` int(11) NOT NULL DEFAULT 0 COMMENT '1被禁用',
  `node_createtime` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `node_updatetime` int(11) NOT NULL DEFAULT 0 COMMENT '修改时间',
  PRIMARY KEY (`node_id`) USING BTREE,
  INDEX `auth_pid`(`node_pid`) USING BTREE,
  INDEX `node_module`(`node_module`) USING BTREE,
  INDEX `node_controller`(`node_controller`) USING BTREE,
  INDEX `node_action`(`node_action`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 119 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '功能节点表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of qf_node
-- ----------------------------
INSERT INTO `qf_node` VALUES (1, '概况', '', 'qfadmin', 'index', 'index', 0, 999, 1, 'el-icon-house', NULL, 0, 0, 1620874188);
INSERT INTO `qf_node` VALUES (2, '运营人员', '', 'qfadmin', '', '', 3, 0, 1, 'el-icon-user', NULL, 0, 0, 1618302083);
INSERT INTO `qf_node` VALUES (3, '系统', '', 'qfadmin', '', '', 0, 0, 1, 'el-icon-data-board', NULL, 0, 0, 1618301765);
INSERT INTO `qf_node` VALUES (4, '配置', '', 'qfadmin', '', '', 0, 0, 1, 'el-icon-setting', NULL, 0, 1617269862, 1712241481);
INSERT INTO `qf_node` VALUES (100, '管理员列表', '', 'qfadmin', 'admin', 'index', 2, 0, 1, 'el-icon-user', '', 0, 0, 1618794624);
INSERT INTO `qf_node` VALUES (101, '用户组管理', '', 'qfadmin', 'group', 'index', 2, 0, 1, '', NULL, 0, 0, 1617246287);
INSERT INTO `qf_node` VALUES (102, '参数配置', '', 'qfadmin', 'conf', 'index', 4, 5, 1, 'el-icon-set-up', '', 0, 0, 1617350626);
INSERT INTO `qf_node` VALUES (104, '菜单管理', '', 'qfadmin', 'node', 'index', 4, 6, 1, 'el-icon-s-operation', '', 0, 0, 1617350880);
INSERT INTO `qf_node` VALUES (105, '附件管理', '', 'qfadmin', 'attach', 'index', 4, 4, 1, 'el-icon-connection', '', 0, 0, 1617345521);
INSERT INTO `qf_node` VALUES (106, '清理数据', '', 'qfadmin', 'system', 'clean', 0, 0, 0, '', '', 0, 0, 1712241480);
INSERT INTO `qf_node` VALUES (107, '基础设置', '', 'qfadmin', 'conf', 'base', 3, 5, 1, 'el-icon-s-operation', '', 0, 0, 1617773467);
INSERT INTO `qf_node` VALUES (108, '资源', '', 'qfadmin', '', '', 0, 1, 1, 'el-icon-files', NULL, 0, 1622538526, 1711117979);
INSERT INTO `qf_node` VALUES (109, '资源管理', '', 'qfadmin', 'source', 'index', 108, 10, 1, 'el-icon-folder-opened', NULL, 0, 1622538567, 1726190121);
INSERT INTO `qf_node` VALUES (112, '账号管理', '', 'qfadmin', 'source', 'deposit', 108, 1, 1, 'el-icon-crop', NULL, 0, 1712112542, 1726195575);
INSERT INTO `qf_node` VALUES (113, '资源日志', '', 'qfadmin', 'source', 'log', 108, 8, 1, 'el-icon-discover', NULL, 0, 1712208103, 1726195583);
INSERT INTO `qf_node` VALUES (114, '用户需求', '', 'qfadmin', 'source', 'feedback', 108, 1, 1, 'el-icon-edit', NULL, 0, 1712230638, 1712230717);
INSERT INTO `qf_node` VALUES (118, '分类管理', '', 'qfadmin', 'source', 'category', 108, 9, 1, 'el-icon-s-operation', NULL, 0, 1716363477, 1726190129);

-- ----------------------------
-- Table structure for qf_source
-- ----------------------------
DROP TABLE IF EXISTS `qf_source`;
CREATE TABLE `qf_source`  (
  `source_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '资源名称',
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '资源地址',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '目前用于副标题 搜索',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT '内容',
  `page_views` int(11) NOT NULL DEFAULT 0 COMMENT '浏览量',
  `is_time` int(11) NOT NULL DEFAULT 0 COMMENT '0正常 1临时文件',
  `is_user` tinyint(3) NOT NULL DEFAULT 0 COMMENT '状态 0=后台添加 1=用户添加',
  `fid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '夸克标识',
  `is_type` int(11) NOT NULL DEFAULT 0 COMMENT '0夸克网盘 1阿里网盘 2百度网盘 3UC网盘 4迅雷网盘',
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '提取码',
  `source_category_id` int(11) NOT NULL DEFAULT 0 COMMENT '分类ID',
  `vod_content` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '资源介绍',
  `vod_pic` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '资源图片',
  `status` tinyint(3) NOT NULL DEFAULT 1 COMMENT '状态 0=禁用 1=启用',
  `is_delete` tinyint(3) NOT NULL DEFAULT 0 COMMENT '是否删除 0=正常 1=软删除',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '修改时间',
  PRIMARY KEY (`source_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '会议管理表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for qf_source_category
-- ----------------------------
DROP TABLE IF EXISTS `qf_source_category`;
CREATE TABLE `qf_source_category`  (
  `source_category_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '分类名称',
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sort` int(11) NOT NULL DEFAULT 0 COMMENT '排序',
  `status` int(11) NOT NULL DEFAULT 0 COMMENT '状态',
  `is_sys` int(11) NOT NULL DEFAULT 0 COMMENT '1时不能删除',
  `is_update` int(11) NOT NULL DEFAULT 1 COMMENT '0不更新 1更新',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '修改时间',
  PRIMARY KEY (`source_category_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '文章分类表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of qf_source_category
-- ----------------------------
INSERT INTO `qf_source_category` VALUES (1, '短剧', '', 999, 0, 1, 1, 1725114376, 1726299215);
INSERT INTO `qf_source_category` VALUES (2, '电影', '', 998, 0, 1, 1, 1725114387, 1726303157);
INSERT INTO `qf_source_category` VALUES (3, '电视剧', '', 997, 0, 1, 1, 1725114393, 1726303158);
INSERT INTO `qf_source_category` VALUES (4, '动漫', '', 996, 0, 1, 1, 1725114400, 1726303159);
INSERT INTO `qf_source_category` VALUES (5, '综艺', '', 995, 0, 1, 1, 1725114408, 1726303160);

-- ----------------------------
-- Table structure for qf_source_log
-- ----------------------------
DROP TABLE IF EXISTS `qf_source_log`;
CREATE TABLE `qf_source_log`  (
  `source_log_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '转存任务名称',
  `total_num` int(11) NOT NULL DEFAULT 0 COMMENT '转存总数',
  `new_num` int(11) NOT NULL DEFAULT 0 COMMENT '新增数',
  `update_num` int(11) NOT NULL DEFAULT 0 COMMENT '更新数(更新资源地址)',
  `skip_num` int(11) NOT NULL DEFAULT 0 COMMENT '重复跳过数',
  `fail_num` int(11) NOT NULL DEFAULT 0 COMMENT '失败数',
  `fail_dec` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '失败原因',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '修改时间',
  `end_time` int(11) NOT NULL DEFAULT 0 COMMENT '结束时间',
  PRIMARY KEY (`source_log_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for qf_token
-- ----------------------------
DROP TABLE IF EXISTS `qf_token`;
CREATE TABLE `qf_token`  (
  `token_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT 0 COMMENT '用户ID',
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'AccessToken',
  `token_expires` int(11) NOT NULL DEFAULT 0 COMMENT '授权码过期时间',
  `platform` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'all' COMMENT '来源终端',
  `ip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '登录IP',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '登录时间',
  PRIMARY KEY (`token_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '授权信息表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for qf_user
-- ----------------------------
DROP TABLE IF EXISTS `qf_user`;
CREATE TABLE `qf_user`  (
  `user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'UID',
  `openid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '微信openid',
  `nickname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户昵称',
  `head_pic` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '头像',
  `sex` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=保密 1=男 2=女',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0=禁用 1=启用',
  `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '修改时间',
  PRIMARY KEY (`user_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '微信用户表' ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
