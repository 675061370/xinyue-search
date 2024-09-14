<?php /*a:5:{s:76:"C:\Users\liu67\Desktop\test\xinyue-search\app\qfadmin\view\source\index.html";i:1726297279;s:77:"C:\Users\liu67\Desktop\test\xinyue-search\app\qfadmin\view\common\header.html";i:1712232785;s:75:"C:\Users\liu67\Desktop\test\xinyue-search\app\qfadmin\view\common\menu.html";i:1712232786;s:77:"C:\Users\liu67\Desktop\test\xinyue-search\app\qfadmin\view\common\footer.html";i:1712232785;s:78:"C:\Users\liu67\Desktop\test\xinyue-search\app\qfadmin\view\component\view.html";i:1712232786;}*/ ?>
<!DOCTYPE html>
<html>

<head>
    <title><?php echo htmlentities($node['node_title']); ?></title>
    <meta charset="UTF-8">
<!-- import CSS -->
<link rel="stylesheet" href="/static/admin/css/element.css">
<link rel="stylesheet" href="/static/admin/css/YAdmin.css">
</head>

<body>
    <div id="app" v-cloak>
        <el-container>
            <el-header>
                <el-col style="width: auto;">
                    <el-menu class="el-menu-vertical" text-color="#333333" :default-active="'<?php if($node['node_pid']): ?><?php echo htmlentities($node['node_pid']); else: ?><?php echo htmlentities($node['node_id']); ?><?php endif; ?>'" unique-opened
                        style="border:none;" mode="horizontal" active-text-color="#333333">
                        <?php if(is_array($menuList) || $menuList instanceof \think\Collection || $menuList instanceof \think\Paginator): $i = 0; $__LIST__ = $menuList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;if(count($item['subList'])>0): if(count($item['subList'][0]['subList'])>0): ?>
                        <el-menu-item index="<?php echo htmlentities($item['node_id']); ?>"
                            onclick="location.href='/<?php echo htmlentities($item['subList'][0]['subList'][0]['node_module']); ?>/<?php echo htmlentities($item['subList'][0]['subList'][0]['node_controller']); ?>/<?php echo htmlentities($item['subList'][0]['subList'][0]['node_action']); ?>';"
                            <?php if($menu==$item['node_id']): ?>class="is-active" <?php endif; ?>>
                            <i class="<?php echo htmlentities($item['node_icon']); ?>"></i> <?php echo htmlentities($item['node_title']); ?>
                        </el-menu-item>
                        <?php else: ?>
                        <el-menu-item index="<?php echo htmlentities($item['node_id']); ?>"
                            onclick="location.href='/<?php echo htmlentities($item['subList'][0]['node_module']); ?>/<?php echo htmlentities($item['subList'][0]['node_controller']); ?>/<?php echo htmlentities($item['subList'][0]['node_action']); ?>';" <?php if($menu==$item['node_id']): ?>class="is-active" <?php endif; ?>>
                            <i class="<?php echo htmlentities($item['node_icon']); ?>"></i> <?php echo htmlentities($item['node_title']); ?>
                        </el-menu-item>
                        <?php endif; else: if(count($item['subList'])>0 || $item['node_controller']=='index'): ?>
                            <el-menu-item index="<?php echo htmlentities($item['node_id']); ?>"
                                onclick="location.href='/<?php echo htmlentities($item['node_module']); ?>/<?php echo htmlentities($item['node_controller']); ?>/<?php echo htmlentities($item['node_action']); ?>';" <?php if($menu==$item['node_id']): ?>class="is-active" <?php endif; ?>>
                                <i class="<?php echo htmlentities($item['node_icon']); ?>"></i> <?php echo htmlentities($item['node_title']); ?>
                            </el-menu-item>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </el-menu>
                </el-col>

                <el-col style="width: 240px;float: right;">
                    <span class="topArea">
                        <el-link :underline="false" class="el-icon-full-screen menuicon" onclick="requestFullScreen()"></el-link>
                        <!-- <el-link :underline="false" class="el-icon-brush menuicon"></el-link> -->
                        <el-dropdown>
                            <el-link :underline="false">&nbsp;<?php echo htmlentities($adminInfo['admin_name']); ?><i class="el-icon-arrow-down"></i></el-link>
                            <el-dropdown-menu slot="dropdown">
                                <el-dropdown-item onclick="location.href='/qfadmin/admin/updatemyinfo';">修改资料
                                </el-dropdown-item>
                                <el-dropdown-item onclick="location.href='/qfadmin/admin/motifypassword';">修改密码
                                </el-dropdown-item>
                                <el-dropdown-item onclick="location.href='/qfadmin/system/clean';">清除缓存
                                </el-dropdown-item>
                                <el-dropdown-item  onclick="logout()">退出登录</el-dropdown-item>
                            </el-dropdown-menu>
                        </el-dropdown>
                    </span>
                </el-col>
            </el-header>
            <el-container class="body">
                <?php if($menuLists): ?>
<el-aside width="160px">
    <a class="titlehome" href="/qfadmin" style="display: block;height: 62px;line-height: 62px;text-align: center;"><img src="/static/admin/images/logo.png" width="40px" style="vertical-align: middle;" /></a>
    <el-menu class="el-menu-vertical-demo" text-color="#333333" default-active="'<?php echo htmlentities($node['node_pid']); ?>-<?php echo htmlentities($node['node_id']); ?>'" :collapse="false" :default-openeds="['<?php echo htmlentities($node['node_pid']); ?>']">
        <?php if(is_array($menuLists) || $menuLists instanceof \think\Collection || $menuLists instanceof \think\Paginator): $i = 0; $__LIST__ = $menuLists;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;if(count($item['subList'])>0): ?>
        <el-submenu index="<?php echo htmlentities($item['node_id']); ?>">
            <template slot="title">
                <i <?php if($item['node_icon']): ?>class="<?php echo htmlentities($item['node_icon']); ?>"<?php else: ?>class="el-icon-menu"<?php endif; ?>></i> <?php echo htmlentities($item['node_title']); ?>
            </template>
            <?php if(is_array($item['subList']) || $item['subList'] instanceof \think\Collection || $item['subList'] instanceof \think\Paginator): $i = 0; $__LIST__ = $item['subList'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$subItem): $mod = ($i % 2 );++$i;?>
            <el-menu-item onclick="location.href='/<?php echo htmlentities($subItem['node_module']); ?>/<?php echo htmlentities($subItem['node_controller']); ?>/<?php echo htmlentities($subItem['node_action']); ?>';"
                index="<?php echo htmlentities($item['node_id']); ?>-<?php echo htmlentities($subItem['node_id']); ?>" <?php if($subItem['node_controller']==strtolower($controller) && $subItem['node_action']==strtolower($action)): ?>class="is-active" <?php endif; ?>><?php echo htmlentities($subItem['node_title']); ?></el-menu-item>
            <?php endforeach; endif; else: echo "" ;endif; ?>
        </el-submenu>
        <?php else: if($item['node_controller']=='' && $item['node_action']==''): ?>
            <el-menu-item index="<?php echo htmlentities($item['node_id']); ?>" onclick="testalert('暂无该功能')">
                <i <?php if($item['node_icon']): ?>class="<?php echo htmlentities($item['node_icon']); ?>" <?php else: ?>class="el-icon-menu" <?php endif; ?>></i>
                <?php echo htmlentities($item['node_title']); ?>
            </el-menu-item>
            <?php else: ?>
            <el-menu-item index="<?php echo htmlentities($item['node_id']); ?>"
                onclick="location.href='/<?php echo htmlentities($item['node_module']); ?>/<?php echo htmlentities($item['node_controller']); ?>/<?php echo htmlentities($item['node_action']); ?>';" <?php if($item['node_controller']==strtolower($controller) && $item['node_action']==strtolower($action)): ?>class="is-active" <?php endif; ?>>
                <i <?php if($item['node_icon']): ?>class="<?php echo htmlentities($item['node_icon']); ?>" <?php else: ?>class="el-icon-menu" <?php endif; ?>></i>
                <?php echo htmlentities($item['node_title']); ?>
            </el-menu-item>
            <?php endif; ?>
        <?php endif; ?>
        <?php endforeach; endif; else: echo "" ;endif; ?>
        <div style="height: 120px;"></div>
    </el-menu>
    <div class="version">资源管理系统<br> Version 1.0.0</div>
</el-aside>
<?php else: ?>
<div style="position: absolute;top: -72px;left: 0;width: 160px;background-color: #ffffff;box-shadow: 0 0 12px 0 rgb(47 75 168 / 6%);border-radius: 12px;">
    <a class="title" href="/admin" style="display: block;height: 62px;line-height: 62px;text-align: center;"><img
            src="/static/admin/images/logo.png" width="40px" style="vertical-align: middle;" /></a>
</div>
<?php endif; ?>

                <el-main>

    <el-form :inline="true" @submit.native.prevent>
        <el-form-item>
            <el-button icon="el-icon-plus" size="small" @click="clickAdd" plain>添加资源</el-button>
            <el-button icon="el-icon-document-copy" size="small" @click="getExport" plain>导出资源</el-button>
            <el-button icon="el-icon-plus" size="small" @click="ImportShow" plain>夸克导入专用</el-button>
            <el-button icon="el-icon-plus" size="small" @click="ImportBatch" plain>批量导入</el-button>
        </el-form-item>
        <div style="float:right">
            <el-form-item style="width:120px;">
                <el-select size="small" v-model="search.source_category_id" placeholder="筛选分类">
                    <el-option v-for="item in category" :key="item.source_category_id" :label="item.name" :value="item.source_category_id">
                    </el-option>
                </el-select>
            </el-form-item>
            <el-form-item>
                <el-input placeholder="输入关键词搜索" size="small" v-model="search.keyword" @keyup.enter.native="getList_search"></el-input>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" icon="el-icon-search" size="small" @click="getList_search" plain>搜索
                </el-button>
                <el-button icon="el-icon-refresh-left" size="small" @click="getList_search(0)" plain>重置</el-button>
            </el-form-item>
        </div>
    </el-form>
    <el-table :data="dataList.data"  v-loading="loading">
        <el-table-column prop="source_id" label="ID" width="80">
        </el-table-column>
        <el-table-column prop="title" label="资源名称">
        </el-table-column>
        <el-table-column prop="source_category_id_name" label="资源分类">
        </el-table-column>
        <el-table-column prop="url" label="资源地址" align="center">
        </el-table-column>
        <el-table-column prop="create_time" label="入库时间" align="center" width="200">
        </el-table-column>
        <el-table-column prop="update_time" label="更新时间" align="center" width="200">
        </el-table-column>
        <el-table-column label="操作" width="180" align="center">
            <template slot-scope="scope">
                <div class="order-text">
                    <p>
                        <el-link type="success" @click="clickEdit(scope.row)" :underline="false">编辑</el-link>
                    </p>
                    <p>
                        <el-link type="danger" @click="clickDelete(scope.row)" :underline="false">删除</el-link>
                    </p>
                </div>
            </template>
        </el-table-column>

    </el-table>


    <div class="page">
        <el-pagination @size-change="handleSizeChange" :page-sizes="[10, 20, 50, 100,200,500]" :page-size="10"
            layout="total, sizes, prev, pager, next, jumper" background @current-change="changeCurrentPage"
            :current-page="dataList.current_page" :page-count="dataList.last_page" :total="dataList.total">
        </el-pagination>
    </div>


    <!-- 添加框 -->
    <el-dialog title="添加资源" :visible.sync="dialogFormAdd" :modal-append-to-body='false' append-to-body
        :close-on-click-modal='false' width="680px">
        <el-form :model="formAdd" :rules="rules" ref="formAdd">
            <el-form-item prop="source_category_id" label="资源分类" :label-width="formLabelWidth">
                <el-select size="medium" v-model="formAdd.source_category_id" placeholder="请选择分类">
                    <el-option v-for="item in category" :key="item.source_category_id" :label="item.name" :value="item.source_category_id">
                    </el-option>
                </el-select>
            </el-form-item>
            <el-form-item prop="title" label="资源名称" :label-width="formLabelWidth">
                <el-input size="medium" autocomplete="off" placeholder="请输入资源名称" v-model="formAdd.title"></el-input>
            </el-form-item>
            <el-form-item prop="url" label="资源地址" :label-width="formLabelWidth">
                <el-input size="medium" autocomplete="off" placeholder="请输入资源地址" v-model="formAdd.url"></el-input>
            </el-form-item>
            <el-form-item label="关键词搜索" :label-width="formLabelWidth">
                <el-input type="textarea" :rows="5" size="medium" autocomplete="off" placeholder="一行一个名称" v-model="formAdd.description"></el-input>
            </el-form-item>
            <el-form-item label="资源介绍" :label-width="formLabelWidth">
                <el-input type="textarea" :rows="5" size="medium" autocomplete="off" placeholder="" v-model="formAdd.vod_content"></el-input>
            </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
            <el-button type="primary" @click="postAdd()">确认添加</el-button>
        </div>
    </el-dialog>
    <!-- 修改框 -->
    <el-dialog title="修改资源" :visible.sync="dialogFormEdit" :modal-append-to-body='false' append-to-body
        :close-on-click-modal='false' width="680px">
        <el-form :model="formEdit" :rules="rules" ref="formEdit">
            <el-form-item prop="source_category_id" label="资源分类" :label-width="formLabelWidth">
                <el-select size="medium" v-model="formEdit.source_category_id" placeholder="请选择分类" clearable>
                    <el-option v-for="item in category" :key="item.source_category_id" :label="item.name"
                        :value="item.source_category_id">
                    </el-option>
                </el-select>
            </el-form-item>
            <el-form-item prop="title" label="资源名称" :label-width="formLabelWidth">
                <el-input size="medium" autocomplete="off" placeholder="请输入资源名称" v-model="formEdit.title"></el-input>
            </el-form-item>
            <el-form-item prop="url" label="资源地址" :label-width="formLabelWidth">
                <el-input size="medium" autocomplete="off" placeholder="请输入资源地址" v-model="formEdit.url"></el-input>
            </el-form-item>
            <el-form-item label="关键词搜索" :label-width="formLabelWidth">
                <el-input type="textarea" :rows="5" size="medium" autocomplete="off" placeholder="一行一个名称" v-model="formEdit.description"></el-input>
            </el-form-item>
            <el-form-item label="资源介绍" :label-width="formLabelWidth">
                <el-input type="textarea" :rows="5" size="medium" autocomplete="off" placeholder="" v-model="formEdit.vod_content"></el-input>
            </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
            <el-button type="primary" @click="postEdit()">确认修改</el-button>
        </div>
    </el-dialog>
    
    <!-- 夸克导入数据 -->
    <el-dialog title="导入资源" :visible.sync="dialogImport" :modal-append-to-body='false' append-to-body
        :close-on-click-modal='false' width="600px">
        <el-form :model="Importform">
            <el-form-item prop="source_category_id" label="资源分类"  label-width="90px">
                <el-select size="medium" v-model="Importform.source_category_id" placeholder="请选择分类" clearable>
                    <el-option v-for="item in category" :key="item.source_category_id" :label="item.name"
                        :value="item.source_category_id">
                    </el-option>
                </el-select>
            </el-form-item>
            <el-form-item label=""  label-width="90px">
                <el-upload class="upload-demo" :data="Importform" name="file" 
                ref="ImportUpload"
                drag 
                :auto-upload="false"
                limit="1"
                :on-exceed="handleExceed"
                :on-success="handleAvatarSuccess"
                action="/admin/source/imports" 
                accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                    <i class="el-icon-upload"></i>
                    <div class="el-upload__text">将文件拖到此处，或<em>点击上传</em></div>
                </el-upload>
                <span style="color: #999;">请导入夸克官方导出的csv文件</span>
            </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
            <el-button type="primary" @click="ImportPost()">提交</el-button>
        </div>
    </el-dialog>
    
    <!-- 批量导入数据 -->
    <el-dialog title="导入资源" :visible.sync="dialogBatch" :modal-append-to-body='false' append-to-body
        :close-on-click-modal='false' width="790px">
        <el-form :model="Batchform">
            <el-form-item prop="type" label="选择方式"  label-width="70px">
                <el-radio-group v-model="Batchform.type">
                  <el-radio-button :label="1">直接导入</el-radio-button>
                  <el-radio-button :label="2">转存分享导入</el-radio-button>
                </el-radio-group>
                <p style="color: #999;" v-if='Batchform.type==1'>直接导入：链接校验有效后直接入库；Tips：该功能不会检测是否重复；</p>
                <p style="color: #999;" v-else-if='Batchform.type==2'>将资源转存到自己网盘后分享入库（<font color=orangered>此功能仅支持夸克</font>）Tips：该功能不会检测是否重复；</p>
                <span style="color: #999;" v-if='Batchform.type==1'>支持<font color=orangered>夸克、阿里、UC</font>的网盘资源(一次最多可以上传500条资源)</span>
                <span style="color: #999;" v-else-if='Batchform.type==2'>目前仅支持<font color=orangered>夸克</font>的网盘资源(一次最多可以上传500条资源)</span>
            </el-form-item>
            <el-form-item prop="source_category_id" label="资源分类" label-width="70px" v-if="Batchform.type">
                <el-select size="medium" v-model="Batchform.source_category_id" placeholder="请选择分类" clearable>
                    <el-option v-for="item in category" :key="item.source_category_id" :label="item.name"
                        :value="item.source_category_id">
                    </el-option>
                </el-select>
                
            </el-form-item>
            
            <el-form-item prop="urls" label="资源分类" label-width="70px" v-if="Batchform.type">
                <el-input
                  type="textarea"
                  placeholder="资源示例：
一条资源一行
https://pan.quark.cn/s/xxxxxxxx
https://www.alipan.com/s/xxxxxxxxx
https://drive.uc.cn/s/xxxxxxxxxxx"
                  v-model="Batchform.urls"
                  rows="20"
                  show-word-limit
                >
                </el-input>
            </el-form-item>
            
  
        </el-form>
        <div slot="footer" class="dialog-footer">
            <el-button type="primary" @click="BatchPost()">提交</el-button>
        </div>
    </el-dialog>

    </el-main>
</el-container>
</el-container>
<Uploads ref="upload"></Uploads>
<Goodslists ref="goodslist"></Goodslists>
</div>
</body>
<template id="Upload">
    <el-dialog title="图片库" :visible.sync="visible" :modal-append-to-body='false' append-to-body :close-on-click-modal="false"
        width="725px">
        <div class="upload-boxs">
            <el-upload class="upload-right" action="/admin/attach/uploadImage" :on-success="handleUploadSuccess"
                :file-list="fileList" :show-file-list="false" :before-upload="beforeUpload" :data="postData"
                v-loading.fullscreen.lock="fullscreenLoading">
                <el-button size="small" type="primary" icon="el-icon-upload">上传图片</el-button>
            </el-upload>
        </div>
        <ul class="storage-list">
            <li :class="item.select?'active':''" v-for="(item, index) in dataList.data" :key="index" @click="select(index)">
                <el-image fit="contain" :src="item.attach_path"></el-image>
                <p>{{item.attach_name}}</p>
            </li>
        </ul>
        <p v-if="dataList.data&&dataList.data.length==0" style="text-align: center;">暂无资源</p>
        <el-pagination @current-change="changeCurrentPage" layout="prev, pager, next" :current-page="dataList.current_page"
            :page-count="dataList.last_page" hide-on-single-page background>
        </el-pagination>
        <div slot="footer" class="dialog-footer">
            <div style="float: left; font-size: 13px;">
                <span v-if="multiple">
                    当前已选 <span style="color: #F56C6C;">{{selectList.length+selected_num}}</span> 个，最多允许选择 <span
                        style="color: #F56C6C;">{{total_num}}</span> 个资源
                </span>
                <span v-else>当前已选 <span style="color: #F56C6C;">{{selectList.length}}</span> 个资源</span>
            </div>
            <el-button @click="visible = false" size="small">取消</el-button>
            <el-button type="primary" @click="save" size="small">确定</el-button>
        </div>
    </el-dialog>
</template>


<template id="Single">
    <div class="slectimg" v-if="multiple">
        <block v-if="value.length>0">
            <draggable v-model="value" chosenClass="chosen" forceFallback="true" animation="600" @start="onStart"
                @end="onEnd">
                <transition-group>
                    <div class="imgs" v-for="(v, s) in value" :key="s" style="cursor: all-scroll;">
                        <el-image fit="contain" :src="v" :preview-src-list="value" :z-index="s"></el-image>
                        <!-- <el-image fit="contain" :src="v" ></el-image> -->
                        <i class="close el-icon-error" @click="deles(s)"></i>
                    </div>
                </transition-group>
            </draggable>
        </block>
        <div class="noimg" @click="selectimg()">
            <i class="el-icon-plus"></i>
        </div>
    </div>
    <div class="slectimg" v-else>
        <div class="imgs" v-if="value.length>0">
            <el-image fit="contain" :src="value" :preview-src-list="[value]"></el-image>
            <i class="close el-icon-error" @click="deles()"></i>
        </div>
        <div class="noimg" v-else @click="selectimg()">
            <i class="el-icon-plus"></i>
        </div>
    </div>
</template>

<template id="Ueditor">
    <Ueditors v-model="value" ref="Ueditor" :config="config"></Ueditors>
</template>


<template id="skuforms">
    <div>
        <div style="padding-bottom: 10px;">
            <el-input style="width: 120px;" v-if="inputVisible" v-model="inputValue" ref="saveTagInput" size="small"
                @keyup.enter.native="handleInputConfirm" placeholder="回车确定">
            </el-input>
            <el-button v-else class="button-new-tag" size="small" @click="showInput" style="width: 120px;">+添加规格组</el-button>
        </div>
        <sku-form :source-attribute="sourceAttribute" :attribute.sync="attribute" :structure="structure" :sku.sync="sku"
            ref="skuForm"></sku-form>
    </div>
</template>


<template id="Goodslist">
    <el-dialog title="商品库" :before-close="handleClose" :visible.sync="visible" :modal-append-to-body='false' append-to-body
        :close-on-click-modal="false" width="900px">
        
        <el-form :inline="true">
            <div style="float:right">
                <el-form-item style="width:100px; margin-bottom: 0;">
                    <el-cascader v-model="search.classify" placeholder="商品分类" :options="categoryList" :props="cascaderProps"
                        style="width: 100%;" filterable clearable size="small" :show-all-levels="false">
                    </el-cascader>
                </el-form-item>
                <el-form-item style="margin-bottom: 0;">
                    <el-input placeholder="输入商品名称搜索" size="small" v-model="search.keyword" @keyup.enter.native="getList_search"
                        clearable @clear="getList_search"></el-input>
                </el-form-item>
                <el-form-item style="margin-bottom: 0;">
                    <el-button type="primary" icon="el-icon-search" size="small" @click="getList_search" plain>搜索
                    </el-button>
                    <el-button icon="el-icon-refresh-left" size="small" @click="getList_search(0)" plain>重置</el-button>
                </el-form-item>
            </div>
        </el-form>

        <el-table :data="dataList.data" ref="multipleTable" @selection-change="changeSelection" row-key="goods_id" reserve-selection="true" style="min-height: 425px;" v-loading="loading">
            <el-table-column align="center" type="selection" reserve-selection="true"  width="55"></el-table-column>

            <el-table-column label="商品" prop="goods_id" min-width="380">
                <template slot-scope="scope">
                    <el-image class="goods-image" style="width: 50px;height: 50px;" :src="scope.row.picture[0]" :preview-src-list="[scope.row.picture[0]]"
                        fit="contain" lazy></el-image>

                    <div class="goods-info cs-ml">
                        <p class="action" style="overflow:hidden;text-overflow:ellipsis;display: -webkit-box;-webkit-box-orient: vertical;-webkit-line-clamp: 2;">
                            <span :title="scope.row.goods_name" class="link">{{scope.row.goods_name}}</span>
                        </p>
                    </div>
                </template>
            </el-table-column>

            <el-table-column label="本店价" prop="goods_price">
                <template slot-scope="scope">
                    <div class="action">
                        <span class="goods-shop-price">{{scope.row.goods_price}}</span>
                    </div>
                </template>
            </el-table-column>

            <el-table-column label="库存" prop="stock_total">
                <template slot-scope="scope">
                    <div class="action">
                        <span>{{scope.row.stock_total}}</span>
                    </div>
                </template>
            </el-table-column>

        </el-table>

        <div slot="footer" class="dialog-footer">
            <p style="padding-bottom: 10px;">
                <el-pagination hide-on-single-page="true" layout="prev, pager, next" background :current-page="form.page" @current-change="changeCurrentPage" :page-size="5" :total="dataList.total">
                </el-pagination>
            </p>
            <p>
                <el-button @click="cancels" size="small">取消</el-button>
                <el-button type="primary" @click="save" size="small">确定</el-button>
            </p>
        </div>
    </el-dialog>
</template>
<script src="/static/admin/js/vue-2.6.10.min.js"></script>
<script src="/static/admin/js/axios.min.js"></script>
<script src="/static/admin/js/element.js"></script>
<script src="/static/admin/js/YAdmin.js"></script>
<script src="/static/admin/js/SkuForm.umd.js"></script>
<script src="/static/admin/UEditor/vue-ueditor-wrap.min.js"></script>
<script src="/static/admin/UEditor/ueditor.config.js"></script>
<script src="/static/admin/UEditor/ueditor.all.js"></script>
<script src="/static/admin/js/component.js"></script>
<script src="/static/admin/js/Sortable.min.js"></script>
<script src="/static/admin/js/vuedraggable.umd.min.js"></script>

    <script>
        var app = new Vue({
            el: '#app',
            data() {
                return {
                    search: {
                        keyword: "",
                        filter: "title",
                        source_category_id: ''
                    },
                    formLabelWidth: '120px',
                    dialogFormAdd: false,
                    dialogFormEdit: false,
                    loading: true,
                    dataList: [],
                    form: {
                        page: 1,
                        per_page: 10,
                        order: 'create_time desc'
                    },
                    formAdd: {
                    },
                    formEdit: {
                    },
                    rules: {
                        title: [{ required: true, message: '请输入资源名称', trigger: 'blur' }],
                        url: [{ required: true, message: '请输入资源地址', trigger: 'blur' }],
                    },
                    dialogImport: false,
                    Importform: {
                        
                    },

                    category: [],
                    
                    dialogBatch: false,
                    Batchform: {},
                }
            },
            created() {
                this.getcategory();
            },
            methods: {
                getList_search(val) {
                    if (val == 0) {
                        this.search = {
                            keyword: "",
                            filter: "title",
                            source_category_id: ''
                        }
                    }
                    this.form.page = 1;
                    this.getList();
                },
                handleSizeChange(per_page) {
                    this.form.per_page = per_page;
                    this.getList();
                },
                postEdit() {
                    var that = this;
                    that.$refs["formEdit"].validate((valid) => {
                        if (valid) {
                            axios.post('/admin/source/update', Object.assign({}, PostBase, that.formEdit))
                                .then(function (response) {
                                    that.getList();
                                    if (response.data.code == CODE_SUCCESS) {
                                        that.$message({
                                            message: response.data.message,
                                            type: 'success'
                                        });
                                        that.dialogFormEdit = false;
                                    } else {
                                        that.$message.error(response.data.message);
                                    }
                                })
                                .catch(function (error) {
                                    that.$message.error('服务器内部错误');
                                    console.log(error);
                                });
                        }
                    });
                },
                postAdd() {
                    var that = this;
                    that.$refs['formAdd'].validate((valid) => {
                        if (valid) {
                            axios.post('/admin/source/add', Object.assign({}, PostBase, that.formAdd))
                                .then(function (response) {
                                    that.getList();
                                    if (response.data.code == CODE_SUCCESS) {
                                        that.$message({
                                            message: response.data.message,
                                            type: 'success'
                                        });
                                        that.dialogFormAdd = false;
                                    } else {
                                        that.$message.error(response.data.message);
                                    }
                                })
                                .catch(function (error) {
                                    that.$message.error('服务器内部错误');
                                });
                        }
                    });
                },
                clickAdd() {
                    var that = this;
                    that.formAdd = { status: 1, share_image: '' };
                    that.dialogFormAdd = true;
                },
                clickDelete(row) {
                    var that = this;
                    this.$confirm('删除后，资源将无法查看，是否继续删除?', '删除提醒', {
                        confirmButtonText: '删除',
                        cancelButtonText: '取消',
                        type: 'warning'
                    }).then(() => {
                        axios.post('/admin/source/delete', Object.assign({}, PostBase, {
                            source_id: row.source_id
                        }))
                            .then(function (response) {
                                that.getList();
                                if (response.data.code == CODE_SUCCESS) {
                                    that.$message({
                                        message: response.data.message,
                                        type: 'success'
                                    });
                                } else {
                                    that.$message.error(response.data.message);
                                }
                            })
                            .catch(function (error) {
                                that.$message.error('服务器内部错误');
                            });
                    }).catch(() => {
                    });
                },
                clickEdit(row) {
                    var that = this;
                    that.formEdit = row;
                    axios.post('/admin/source/detail', Object.assign({}, PostBase, {
                        source_id: row.source_id
                    }))
                        .then(function (response) {
                            if (response.data.code == CODE_SUCCESS) {
                                response.data.data.source_category_id = response.data.data.source_category_id || undefined
                                that.formEdit = response.data.data;
                                that.dialogFormEdit = true;
                            } else {
                                that.$message.error(response.data.message);
                            }
                        })
                        .catch(function (error) {
                            that.$message.error('服务器内部错误');
                        });
                },
                changeCurrentPage(page) {
                    this.form.page = page;
                    this.getList();
                },
                getcategory(){
                    var that = this;
                    axios.post('/admin/source_category/getList', Object.assign({}, PostBase))
                        .then(function (response) {
                            that.loading = false;
                            if (response.data.code == CODE_SUCCESS) {
                                that.category = response.data.data;
                                that.getList();
                            } else {
                                that.$message.error(response.data.message);
                            }
                        })
                        .catch(function (error) {
                            that.loading = false;
                            that.$message.error('服务器内部错误');
                            console.log(error);
                        });
                },
                getList() {
                    var that = this;
                    that.loading = true;
                    axios.post('/admin/source/getList', Object.assign({}, PostBase, that.form, that.search))
                        .then(function (response) {
                            that.loading = false;
                            if (response.data.code == CODE_SUCCESS) {
                                that.dataList = response.data.data;
                                that.setcategory()
                            } else {
                                that.$message.error(response.data.message);
                            }
                        })
                        .catch(function (error) {
                            that.loading = false;
                            that.$message.error('服务器内部错误');
                        });
                },
                setcategory(){
                    for (let item of this.dataList.data) {
                        for (let items of this.category) {
                            if(item.source_category_id == items.source_category_id){
                                item.source_category_id_name = items.name
                            }
                        }
                    }
                },
                
                //导入数据
                ImportShow() {
                    this.Importform = {
                    }
                    this.dialogImport = true
                },
                handleExceed(files, fileList) {
                    this.$message.warning(`只能选择一个文件`);
                },
                handleAvatarSuccess(res, file) {
                    if (res.code == 200) {
                        this.getList();
                        this.$message({
                            message: res.message,
                            type: 'success'
                        });
                        this.dialogImport = false;
                    } else {
                        this.$message.error(res.message);
                    }
                    this.$refs.ImportUpload.clearFiles();
                },
                ImportPost(){
                    this.Importform = Object.assign(this.Importform, PostBase)
                    this.$nextTick(() => {
                        this.$refs.ImportUpload.submit();
                    })
                },
                
                //批量导入数据
                ImportBatch() {
                    this.Batchform = {}
                    this.dialogBatch = true
                },
                BatchPost(){
                    var that = this;
                    if(!that.Batchform.type) return that.$message.error('请选择导入方式');
                    if(!that.Batchform.urls) return that.$message.error('请输入资源地址');
                    axios.post('/admin/source/transfer', Object.assign({}, PostBase, that.Batchform))
                        .then(function (res) {
                        })
                        .catch(function (error) {
                            that.$message.error('服务器内部错误');
                        });
                    that.$message({
                        message: "已提交任务，稍后查看结果",
                        type: 'success'
                    });
                },
                

                //数据导出
                getExport(){
                    var that = this;
                    var filters = Object.assign({}, PostBase, that.search);
                    var url = '/admin/source/excel?';
                    for (let key in filters) {
                        url += key + "=" + filters[key] + "&";
                    }
                    window.open(url);
                    that.$message.success('数据导出成功');
                },
            }
        })
    </script>


</html>