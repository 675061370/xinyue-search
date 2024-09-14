## 心悦搜索

免费分享百万级网盘资源，致力打造顶尖网盘搜索引擎，让您畅享资源无忧！

## 更新日志

### v2

- UI改版：不再使用uniapp
- 优化Seo：增加后台配置seo参数、伪静态网址、网站地图等
- 自定义首页背景图、背景色等样式
- 优化搜索模式：支持精准搜索、模糊搜索、分词搜索
- 增加转存过滤删除广告的功能
- 增加批量导入转存功能
- 支持多网盘导入功能(目前仅夸克支持转存分享)
- 增加资源分类功能

注：鉴于数据库改动大，最快升级方式：1、重新搭建新项目；2、旧项目后台导出资源表格；3、新项目导入这个表格

## 演示

[前端体验](https://pan.xinyuedh.com)

<https://pan.xinyuedh.com>


## 后台安装教程

0、PHP（选择7.2，其它版本不兼容） 

1、上传源码到服务器

2、设置网站运行目录public

3、设置thinkphp伪静态

4、导入数据库文件

5、修改.env文件数据库参数

后台地址：https://你的域名/qfadmin
账号密码：admin 123456

## 常见问题

1、全部转存执行1分钟~5分钟后中断问题，修改超时限制
该操作用时很长，请设置最大值86400   设置后需重启下服务
宝塔设置教程 https://www.kancloud.cn/loveouu/bthelp/1541867

2、非7.2版本导致的报错，请自行解决，不要再问！！！不会就百度或者老老实实用7.2

3、nginx 404 Not Found  伪静态设置
```shell
location ~* (runtime|application)/{
	return 403;
}
location / {
	if (!-e $request_filename){
		rewrite  ^(.*)$  /index.php?s=$1  last;   break;
	}
}
```

## 后台管理截图

1、一键转存他人链接：就是将别人的分享链接转为你自己的

2、转存心悦搜剧资源：就是将心悦搜剧平台上的所有资源都转成你自己的

3、每日自动更新：自动转存每天的资源并入库

![image](https://pan.xinyuedh.com/1.png)

![image](https://pan.xinyuedh.com/2.png)

![image](https://pan.xinyuedh.com/3.png)


## 如何获取夸克网盘Cookie

登录夸克网盘后，按下F12，刷新页面

![image](https://pan.xinyuedh.com/cookie.jpg)


# 免费交流社群

请加微信l1417716300，请备注来源

程序使用不收费，但不负责搭建，搭建过程遇到问题，可以私聊或群里咨询



