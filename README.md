## ❤️心悦搜索

免费分享百万级网盘资源，致力打造顶尖网盘搜索引擎，让您畅享资源无忧！

## 🔔温馨提示

📌 本项目仅供技术交流与学习使用，项目本身不存储、不提供任何资源文件或下载链接。

📌 请勿将本项目用于任何非法用途，否则后果自负。

如有任何问题或建议，欢迎交流探讨！ 😊

## 更新日志

### v2.1

- 增加网页全网搜功能
- 对接微信对话功能
- 更新优化Seo
- 后台增加批量删除功能
- 优化表格导入功能

注：如何更新见文件夹updateLog/24.11.4

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

0、系统不再支持全部转存及每日更新，有能力者可自行修改使用

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

4、网站经常打不开，出现500的情况，未具体找到原因，可能是因为服务器配置太低，分词功能占用内存过高导致的
升级服务器配置或后台修改搜索模式改为“精准搜索”；


5、全网搜的内容为临时分享资源，需要配置计划任务 每5分钟执行一次 即可
https://XXXXX/api/other/delete_search

## 前台截图

![image](github/p3.png)

![image](github/p2.png)

![image](github/p1.png)

## 后台管理截图

![image](github/1.png)

![image](github/2.png)


## 如何获取夸克网盘Cookie

登录夸克网盘后，按下F12，刷新页面

![image](github/cookie.jpg)


# 免费交流社群

可以进交流群,一起交流学习，添加时请备注来源（如果项目对你有所帮助，也可以请我喝杯咖啡 ☕️ ~）
请加微信l1417716300


| <img src="github/qr1.jpg" width="180px"> | <img src="github/qr9.jpg" width="180px"> |
| --- | --- |

程序使用不收费，但不负责搭建，搭建过程遇到问题，可以私聊或群里咨询



