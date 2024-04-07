-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2024-04-04 20:43:04
-- 服务器版本： 5.7.26
-- PHP 版本： 7.3.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `www_dj_com`
--

-- --------------------------------------------------------

--
-- 表的结构 `qf_access`
--

CREATE TABLE `qf_access` (
  `access_id` int(11) NOT NULL,
  `access_admin` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `access_token` varchar(255) NOT NULL DEFAULT '' COMMENT 'AccessToken',
  `access_plat` varchar(255) NOT NULL DEFAULT 'all' COMMENT '登录平台',
  `access_ip` varchar(255) NOT NULL DEFAULT '' COMMENT 'IP',
  `access_status` int(11) NOT NULL DEFAULT '0' COMMENT '状态',
  `access_createtime` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `access_updatetime` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='授权信息表';

--
-- 转存表中的数据 `qf_access`
--

INSERT INTO `qf_access` (`access_id`, `access_admin`, `access_token`, `access_plat`, `access_ip`, `access_status`, `access_createtime`, `access_updatetime`) VALUES
(1, 1, 'cb12654681713cfbaf0083826c6f60e1df14a3d7100000cb12654681713cfbaf0083826c6f60e1df14a3d7', 'admin', '61.136.79.242', 1, 1712108461, 1712137607),
(2, 1, '239f1a9c7fe1aab2ccc2bd7bee05e007ef35669f100000239f1a9c7fe1aab2ccc2bd7bee05e007ef35669f', 'admin', '61.136.79.242', 1, 1712137847, 1712137925),
(3, 1, 'e74a15119c2d1221aa3b24870646524bab197fe3100000e74a15119c2d1221aa3b24870646524bab197fe3', 'admin', '127.0.0.1', 1, 1712199252, 1712233520),
(4, 1, '7eabdae80e86156343c58b212fc80df5a92585851000007eabdae80e86156343c58b212fc80df5a9258585', 'admin', '127.0.0.1', 0, 1712233596, 1712234379);

-- --------------------------------------------------------

--
-- 表的结构 `qf_admin`
--

CREATE TABLE `qf_admin` (
  `admin_id` int(11) NOT NULL COMMENT 'UID',
  `admin_account` varchar(64) CHARACTER SET utf8 NOT NULL COMMENT '帐号',
  `admin_password` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '密码',
  `admin_salt` varchar(4) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '密码盐',
  `admin_name` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '用户昵称',
  `admin_idcard` varchar(18) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '身份证',
  `admin_truename` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '真实姓名',
  `admin_email` varchar(64) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '邮箱',
  `admin_money` decimal(9,2) NOT NULL DEFAULT '0.00' COMMENT '余额',
  `admin_group` int(11) NOT NULL DEFAULT '0' COMMENT '用户组',
  `admin_ipreg` varchar(255) NOT NULL COMMENT '注册IP',
  `admin_status` int(11) NOT NULL DEFAULT '0' COMMENT '1被禁用',
  `admin_createtime` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `admin_updatetime` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户表';

--
-- 转存表中的数据 `qf_admin`
--

INSERT INTO `qf_admin` (`admin_id`, `admin_account`, `admin_password`, `admin_salt`, `admin_name`, `admin_idcard`, `admin_truename`, `admin_email`, `admin_money`, `admin_group`, `admin_ipreg`, `admin_status`, `admin_createtime`, `admin_updatetime`) VALUES
(1, 'admin', 'bde25145afb5643cb45365783183f7b8ef46640f', 'nwYn', '超级管理员', '', '超级管理员', '', '0.00', 1, '127.0.0.1', 0, 0, 1712234379);

-- --------------------------------------------------------

--
-- 表的结构 `qf_attach`
--

CREATE TABLE `qf_attach` (
  `attach_id` int(11) NOT NULL,
  `attach_name` varchar(255) DEFAULT NULL COMMENT '文件名',
  `attach_path` varchar(255) NOT NULL DEFAULT '' COMMENT '路径',
  `attach_type` varchar(255) NOT NULL DEFAULT '' COMMENT '类型',
  `attach_size` int(11) NOT NULL DEFAULT '0' COMMENT '大小',
  `attach_admin` int(11) NOT NULL DEFAULT '0' COMMENT '用户',
  `attach_status` int(11) NOT NULL DEFAULT '0' COMMENT '状态',
  `attach_createtime` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `attach_updatetime` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='附件表';

--
-- 转存表中的数据 `qf_attach`
--

INSERT INTO `qf_attach` (`attach_id`, `attach_name`, `attach_path`, `attach_type`, `attach_size`, `attach_admin`, `attach_status`, `attach_createtime`, `attach_updatetime`) VALUES
(3, 'weixinq.png', '/uploads/image/20240328/e3661f5c8797a604c52ded5b8b278bed.png', 'png', 206556, 1, 0, 1711636964, 1711636964),
(5, '微信图片_20240402113107.jpg', '/uploads/image/20240402/5f3dcb0d0cf6d567f40cd7356079eb64.jpg', 'jpg', 107999, 1, 0, 1712028717, 1712028717);

-- --------------------------------------------------------

--
-- 表的结构 `qf_auth`
--

CREATE TABLE `qf_auth` (
  `auth_id` bigint(20) NOT NULL COMMENT '权限ID',
  `auth_group` int(11) NOT NULL DEFAULT '0' COMMENT '权限管理组',
  `auth_node` int(11) NOT NULL DEFAULT '0' COMMENT '功能ID',
  `auth_status` int(11) NOT NULL DEFAULT '0' COMMENT '1被禁用',
  `auth_createtime` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `auth_updatetime` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='权限表';

-- --------------------------------------------------------

--
-- 表的结构 `qf_conf`
--

CREATE TABLE `qf_conf` (
  `conf_id` int(11) NOT NULL,
  `conf_key` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '参数名',
  `conf_value` text CHARACTER SET utf8 COMMENT '参数值',
  `conf_title` varchar(255) CHARACTER SET utf8 DEFAULT '' COMMENT '参数名称',
  `conf_desc` varchar(255) CHARACTER SET utf8 DEFAULT '' COMMENT '参数描述',
  `conf_int` int(11) NOT NULL DEFAULT '0' COMMENT '参数到期',
  `conf_spec` int(11) NOT NULL DEFAULT '0' COMMENT '文本类型',
  `conf_content` varchar(255) DEFAULT NULL COMMENT '单选多选等文本类型的数据集',
  `conf_type` int(11) NOT NULL DEFAULT '0' COMMENT '配置分类 ',
  `conf_status` int(11) NOT NULL DEFAULT '0' COMMENT '显示隐藏 0是隐藏',
  `conf_sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `conf_system` int(11) NOT NULL DEFAULT '0' COMMENT '1为系统参数，请勿删除',
  `conf_createtime` int(11) NOT NULL DEFAULT '0',
  `conf_updatetime` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='配置表';

--
-- 转存表中的数据 `qf_conf`
--

INSERT INTO `qf_conf` (`conf_id`, `conf_key`, `conf_value`, `conf_title`, `conf_desc`, `conf_int`, `conf_spec`, `conf_content`, `conf_type`, `conf_status`, `conf_sort`, `conf_system`, `conf_createtime`, `conf_updatetime`) VALUES
(1, 'app_name', '资源管理系统', '网站名称', '', 0, 0, NULL, 0, 1, 99, 1, 0, 1620610921),
(2, 'upload_max_file', '4097152', '最大文件上传限制', '', 0, 0, NULL, 2, 1, 0, 1, 0, 1617352067),
(3, 'upload_file_type', 'xls,xlsx,csv', '允许文件上传类型', '', 0, 0, NULL, 2, 1, 0, 1, 0, 1617351959),
(4, 'upload_max_image', '2097152', '最大图片上传限制', '', 0, 0, NULL, 2, 1, 0, 1, 0, 1617351961),
(5, 'upload_image_type', 'jpg,png,gif,jpeg,bmp', '允许上传图片类型', '', 0, 0, NULL, 2, 1, 0, 1, 0, 1617351964),
(6, 'sms_sign', '', '百度SMS签名', '', 0, 0, NULL, 3, 1, 0, 1, 0, 1617347740),
(7, 'sms_tmpl_1', '', '百度SMS通知模板ID', '', 0, 0, NULL, 3, 1, 0, 1, 0, 1712108519),
(8, 'sms_tmpl_2', '', '百度SMS短信验证模板ID', '', 0, 0, NULL, 3, 1, 0, 1, 0, 1712108525),
(18, 'mp_appid', '', '公众号appid', '', 0, 0, NULL, 8, 1, 1, 1, 1619170883, 1619170912),
(19, 'mp_appsecret', '', '公众号appsecret', '', 0, 0, NULL, 8, 1, 1, 1, 1619170903, 1619170915),
(20, 'baidu_token', '', '百度短链接Token', '', 0, 0, NULL, 3, 1, 0, 1, 1622862241, 1622862241),
(21, 'logo', '', '网站LOGO', '方形LOGO，最佳显示尺寸为80*80像素', 0, 4, NULL, 0, 1, 0, 0, 1711636952, 1712451684),
(22, 'quark_cookie', '', '夸克Cookie', '', 0, 0, NULL, 4, 1, 0, 1, 1712114435, 1712114652),
(23, 'qcode', '/uploads/image/20240402/5f3dcb0d0cf6d567f40cd7356079eb64.jpg', '群二维码', '微信群二维码', 0, 4, NULL, 0, 1, 0, 0, 1712451616, 1712451616),
(24, 'app_description', '免费分享百万级网盘资源，致力打造顶尖网盘搜索引擎，让您畅享资源无忧！', '网站描述', '用于首页副标题，如：免费分享百万级网盘资源，致力打造顶尖网盘搜索引擎，让您畅享资源无忧！', 0, 0, NULL, 0, 1, 1, 0, 1712451778, 1712451790);

-- --------------------------------------------------------

--
-- 表的结构 `qf_feedback`
--

CREATE TABLE `qf_feedback` (
  `id` int(11) NOT NULL,
  `content` varchar(255) DEFAULT NULL,
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `qf_feedback`
--

INSERT INTO `qf_feedback` (`id`, `content`, `create_time`, `update_time`) VALUES
(1, '我啥都不想看', 1712231175, 1712231175);

-- --------------------------------------------------------

--
-- 表的结构 `qf_group`
--

CREATE TABLE `qf_group` (
  `group_id` int(11) NOT NULL,
  `group_name` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '管理组名称',
  `group_desc` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '管理组描述',
  `group_status` int(11) NOT NULL DEFAULT '0' COMMENT '1被禁用',
  `group_createtime` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `group_updatetime` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='管理组表';

--
-- 转存表中的数据 `qf_group`
--

INSERT INTO `qf_group` (`group_id`, `group_name`, `group_desc`, `group_status`, `group_createtime`, `group_updatetime`) VALUES
(1, '超级管理员', '不允许删除', 0, 0, 1575903468);

-- --------------------------------------------------------

--
-- 表的结构 `qf_node`
--

CREATE TABLE `qf_node` (
  `node_id` int(11) NOT NULL COMMENT '功能ID',
  `node_title` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '功能名称',
  `node_desc` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '功能描述',
  `node_module` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT 'api' COMMENT '模块',
  `node_controller` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '控制器',
  `node_action` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '方法',
  `node_pid` int(11) NOT NULL DEFAULT '0' COMMENT '父ID',
  `node_order` int(11) NOT NULL DEFAULT '0' COMMENT '排序ID',
  `node_show` int(11) NOT NULL DEFAULT '1' COMMENT '1显示到菜单',
  `node_icon` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '图标',
  `node_extend` text CHARACTER SET utf8 COMMENT '扩展数据',
  `node_status` int(11) NOT NULL DEFAULT '0' COMMENT '1被禁用',
  `node_createtime` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `node_updatetime` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='功能节点表';

--
-- 转存表中的数据 `qf_node`
--

INSERT INTO `qf_node` (`node_id`, `node_title`, `node_desc`, `node_module`, `node_controller`, `node_action`, `node_pid`, `node_order`, `node_show`, `node_icon`, `node_extend`, `node_status`, `node_createtime`, `node_updatetime`) VALUES
(1, '概况', '', 'qfadmin', 'index', 'index', 0, 999, 1, 'el-icon-house', NULL, 0, 0, 1620874188),
(2, '运营人员', '', 'qfadmin', '', '', 3, 0, 1, 'el-icon-user', NULL, 0, 0, 1618302083),
(3, '系统', '', 'qfadmin', '', '', 0, 0, 1, 'el-icon-data-board', NULL, 0, 0, 1618301765),
(4, '配置', '', 'qfadmin', '', '', 0, 0, 0, 'el-icon-setting', NULL, 0, 1617269862, 1712213372),
(100, '管理员列表', '', 'qfadmin', 'admin', 'index', 2, 0, 1, 'el-icon-user', '', 0, 0, 1618794624),
(101, '用户组管理', '', 'qfadmin', 'group', 'index', 2, 0, 1, '', NULL, 0, 0, 1617246287),
(102, '参数配置', '', 'qfadmin', 'conf', 'index', 4, 5, 1, 'el-icon-set-up', '', 0, 0, 1617350626),
(104, '菜单管理', '', 'qfadmin', 'node', 'index', 4, 6, 1, 'el-icon-s-operation', '', 0, 0, 1617350880),
(105, '附件管理', '', 'qfadmin', 'attach', 'index', 4, 4, 1, 'el-icon-connection', '', 0, 0, 1617345521),
(106, '清理数据', '', 'qfadmin', 'system', 'clean', 0, 0, 0, '', '', 0, 0, 1620976142),
(107, '基础设置', '', 'qfadmin', 'conf', 'base', 3, 5, 1, 'el-icon-s-operation', '', 0, 0, 1617773467),
(108, '资源', '', 'qfadmin', '', '', 0, 1, 1, 'el-icon-files', NULL, 0, 1622538526, 1711117979),
(109, '资源管理', '', 'qfadmin', 'source', 'index', 108, 1, 1, 'el-icon-folder-opened', NULL, 0, 1622538567, 1712213423),
(112, '转存管理', '', 'qfadmin', 'source', 'deposit', 108, 1, 1, 'el-icon-crop', NULL, 0, 1712112542, 1712213455),
(113, '转存日志', '', 'qfadmin', 'source', 'log', 108, 1, 1, 'el-icon-discover', NULL, 0, 1712208103, 1712213467),
(114, '用户需求', '', 'qfadmin', 'source', 'feedback', 108, 1, 1, 'el-icon-edit', NULL, 0, 1712230638, 1712230717);

-- --------------------------------------------------------

--
-- 表的结构 `qf_source`
--

CREATE TABLE `qf_source` (
  `source_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '资源名称',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '资源地址',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  `content` text COMMENT '内容',
  `page_views` int(11) NOT NULL DEFAULT '0' COMMENT '浏览量',
  `status` tinyint(3) NOT NULL DEFAULT '1' COMMENT '状态 0=禁用 1=启用',
  `is_delete` tinyint(3) NOT NULL DEFAULT '0' COMMENT '是否删除 0=正常 1=软删除',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='会议管理表';

-- --------------------------------------------------------

--
-- 表的结构 `qf_source_log`
--

CREATE TABLE `qf_source_log` (
  `source_log_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '转存任务名称',
  `total_num` int(11) NOT NULL DEFAULT '0' COMMENT '转存总数',
  `new_num` int(11) NOT NULL DEFAULT '0' COMMENT '新增数',
  `update_num` int(11) NOT NULL DEFAULT '0' COMMENT '更新数(更新资源地址)',
  `skip_num` int(11) NOT NULL DEFAULT '0' COMMENT '重复跳过数',
  `fail_num` int(11) NOT NULL DEFAULT '0' COMMENT '失败数',
  `fail_dec` varchar(255) DEFAULT NULL COMMENT '失败原因',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间',
  `end_time` int(11) NOT NULL DEFAULT '0' COMMENT '结束时间'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `qf_token`
--

CREATE TABLE `qf_token` (
  `token_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `token` varchar(255) NOT NULL DEFAULT '' COMMENT 'AccessToken',
  `token_expires` int(11) NOT NULL DEFAULT '0' COMMENT '授权码过期时间',
  `platform` varchar(255) NOT NULL DEFAULT 'all' COMMENT '来源终端',
  `ip` varchar(255) NOT NULL DEFAULT '' COMMENT '登录IP',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '登录时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='授权信息表';

-- --------------------------------------------------------

--
-- 表的结构 `qf_user`
--

CREATE TABLE `qf_user` (
  `user_id` int(11) NOT NULL COMMENT 'UID',
  `openid` varchar(255) NOT NULL DEFAULT '' COMMENT '微信openid',
  `nickname` varchar(255) NOT NULL DEFAULT '' COMMENT '用户昵称',
  `head_pic` varchar(512) NOT NULL DEFAULT '' COMMENT '头像',
  `sex` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=保密 1=男 2=女',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0=禁用 1=启用',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='微信用户表';

--
-- 转储表的索引
--

--
-- 表的索引 `qf_access`
--
ALTER TABLE `qf_access`
  ADD PRIMARY KEY (`access_id`) USING BTREE;

--
-- 表的索引 `qf_admin`
--
ALTER TABLE `qf_admin`
  ADD PRIMARY KEY (`admin_id`) USING BTREE,
  ADD KEY `admin_group` (`admin_group`) USING BTREE,
  ADD KEY `admin_name` (`admin_name`) USING BTREE,
  ADD KEY `admin_password` (`admin_password`) USING BTREE,
  ADD KEY `admin_account` (`admin_account`) USING BTREE;

--
-- 表的索引 `qf_attach`
--
ALTER TABLE `qf_attach`
  ADD PRIMARY KEY (`attach_id`) USING BTREE;

--
-- 表的索引 `qf_auth`
--
ALTER TABLE `qf_auth`
  ADD PRIMARY KEY (`auth_id`) USING BTREE,
  ADD KEY `role_group` (`auth_group`) USING BTREE,
  ADD KEY `role_auth` (`auth_node`) USING BTREE;

--
-- 表的索引 `qf_conf`
--
ALTER TABLE `qf_conf`
  ADD PRIMARY KEY (`conf_id`) USING BTREE,
  ADD KEY `conf_key` (`conf_key`) USING BTREE;

--
-- 表的索引 `qf_feedback`
--
ALTER TABLE `qf_feedback`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `qf_group`
--
ALTER TABLE `qf_group`
  ADD PRIMARY KEY (`group_id`) USING BTREE;

--
-- 表的索引 `qf_node`
--
ALTER TABLE `qf_node`
  ADD PRIMARY KEY (`node_id`) USING BTREE,
  ADD KEY `auth_pid` (`node_pid`) USING BTREE,
  ADD KEY `node_module` (`node_module`) USING BTREE,
  ADD KEY `node_controller` (`node_controller`) USING BTREE,
  ADD KEY `node_action` (`node_action`) USING BTREE;

--
-- 表的索引 `qf_source`
--
ALTER TABLE `qf_source`
  ADD PRIMARY KEY (`source_id`) USING BTREE;

--
-- 表的索引 `qf_source_log`
--
ALTER TABLE `qf_source_log`
  ADD PRIMARY KEY (`source_log_id`);

--
-- 表的索引 `qf_token`
--
ALTER TABLE `qf_token`
  ADD PRIMARY KEY (`token_id`) USING BTREE;

--
-- 表的索引 `qf_user`
--
ALTER TABLE `qf_user`
  ADD PRIMARY KEY (`user_id`) USING BTREE;

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `qf_access`
--
ALTER TABLE `qf_access`
  MODIFY `access_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- 使用表AUTO_INCREMENT `qf_admin`
--
ALTER TABLE `qf_admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'UID', AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `qf_attach`
--
ALTER TABLE `qf_attach`
  MODIFY `attach_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- 使用表AUTO_INCREMENT `qf_auth`
--
ALTER TABLE `qf_auth`
  MODIFY `auth_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '权限ID';

--
-- 使用表AUTO_INCREMENT `qf_conf`
--
ALTER TABLE `qf_conf`
  MODIFY `conf_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- 使用表AUTO_INCREMENT `qf_feedback`
--
ALTER TABLE `qf_feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `qf_group`
--
ALTER TABLE `qf_group`
  MODIFY `group_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `qf_node`
--
ALTER TABLE `qf_node`
  MODIFY `node_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '功能ID', AUTO_INCREMENT=115;

--
-- 使用表AUTO_INCREMENT `qf_source`
--
ALTER TABLE `qf_source`
  MODIFY `source_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `qf_source_log`
--
ALTER TABLE `qf_source_log`
  MODIFY `source_log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `qf_token`
--
ALTER TABLE `qf_token`
  MODIFY `token_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `qf_user`
--
ALTER TABLE `qf_user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'UID';
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
