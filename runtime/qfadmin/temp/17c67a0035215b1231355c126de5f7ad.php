<?php /*a:5:{s:74:"C:\Users\liu67\Desktop\test\xinyue-search\app\qfadmin\view\conf\index.html";i:1725324955;s:77:"C:\Users\liu67\Desktop\test\xinyue-search\app\qfadmin\view\common\header.html";i:1712232785;s:75:"C:\Users\liu67\Desktop\test\xinyue-search\app\qfadmin\view\common\menu.html";i:1712232786;s:77:"C:\Users\liu67\Desktop\test\xinyue-search\app\qfadmin\view\common\footer.html";i:1712232785;s:78:"C:\Users\liu67\Desktop\test\xinyue-search\app\qfadmin\view\component\view.html";i:1712232786;}*/ ?>
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

    <el-form :inline="true">
        <el-form-item>
            <el-button icon="el-icon-plus" size="small" @click="clickAdd" plain>添加</el-button>
        </el-form-item>
        <div style="float:right">
            <el-form-item style="width:120px;">
                <el-select placeholder="筛选类别" size="small" v-model="search.filter">
                    <el-option value="conf_id" label="参数ID">
                    </el-option>
                    <el-option value="conf_title" label="参数名称">
                    </el-option>
                    <el-option value="conf_key" label="参数字段">
                    </el-option>
                </el-select>
            </el-form-item>
            <el-form-item>
                <el-input placeholder="输入关键词搜索" size="small" v-model="search.keyword"></el-input>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" icon="el-icon-search" size="small" @click="getList_search" plain>搜索</el-button>
            </el-form-item>
        </div>
    </el-form>
    <el-table :data="dataList.data" @selection-change="changeSelection" v-loading="loading">
        <el-table-column prop="conf_id" label="ID" width="60">
        </el-table-column>
        <el-table-column prop="conf_type" label="所属分类" width="150">
            <template slot-scope="scope">
                <el-tag size="small" type="success" v-if="scope.row.conf_type==1">搜索设置</el-tag>
                <el-tag size="small" type="warning" v-else-if="scope.row.conf_type==2">上传配置</el-tag>
                <el-tag size="small" type="info" v-else-if="scope.row.conf_type==3">前端模版</el-tag>
                <el-tag size="small" type="danger" v-else-if="scope.row.conf_type==4">其他配置</el-tag>
                <el-tag size="small" type="success" v-else-if="scope.row.conf_type==5">微信支付配置</el-tag>
                <el-tag size="small" type="warning" v-else-if="scope.row.conf_type==6">支付宝支付配置</el-tag>
                <el-tag size="small" type="success" v-else-if="scope.row.conf_type==7">微信小程序配置</el-tag>
                <el-tag size="small" type="danger" v-else-if="scope.row.conf_type==8">微信公众号配置</el-tag>
                <el-tag size="small" type="danger" v-else-if="scope.row.conf_type==9">SEO设置</el-tag>
                <el-tag size="small" type="danger" v-else-if="scope.row.conf_type==10">交易设置</el-tag>
                <el-tag size="small" type="danger" v-else-if="scope.row.conf_type==11">售后设置</el-tag>
                <el-tag size="small" v-else>基础设置</el-tag>
            </template>
        </el-table-column>
        <el-table-column prop="conf_title" label="参数名称">
        </el-table-column>
        <el-table-column prop="conf_desc" label="参数描述">
        </el-table-column>
        <el-table-column prop="conf_key" label="参数字段">
            <template slot-scope="scope">
                <el-tooltip class="item" effect="dark" :content="scope.row.conf_title" placement="top">
                    <el-link>{{scope.row.conf_key}}</el-link>
                </el-tooltip>
            </template>
        </el-table-column>
        <el-table-column prop="conf_sort" label="排序" width="90">
        </el-table-column>
        <el-table-column label="操作" width="180">
            <template slot-scope="scope">
                <el-link type="primary" @click="clickEdit(scope.row)" :underline="false">编辑</el-link>&nbsp;
                <el-link type="danger" @click="clickDelete(scope.row)" :underline="false" v-if="scope.row.conf_system==0">删除</el-link>
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
    <el-dialog title="添加配置" :visible.sync="dialogFormAdd" width="800px" :modal-append-to-body='false' append-to-body :close-on-click-modal='false'>
        <el-form :model="formAdd" status-icon :rules="rules" ref="formAdd">
            <el-form-item label="所属分类" :label-width="formLabelWidth" prop="conf_type">
                <el-select v-model="formAdd.conf_type" placeholder="请选择所属分类" size="medium">
                    <el-option label="基础设置" value="0"></el-option>
                    <el-option label="SEO设置" value="9"></el-option>
                    <el-option label="搜索设置" value="1"></el-option>
                    <el-option label="上传配置" value="2"></el-option>
                    <el-option label="前端模版" value="3"></el-option>
                    <el-option label="其他配置" value="4"></el-option>
                    <el-option label="微信支付配置" value="5"></el-option>
                    <el-option label="支付宝支付配置" value="6"></el-option>
                    <el-option label="微信小程序配置" value="7"></el-option>
                    <el-option label="微信公众号配置" value="8"></el-option>
                    <el-option label="交易设置" value="10"></el-option>
                    <el-option label="售后设置" value="11"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="参数名称" :label-width="formLabelWidth" prop="conf_title">
                <el-input size="medium" autocomplete="off" v-model="formAdd.conf_title"></el-input>
                <span style="color: #999;">参数名称，如：网站LOGO</span>
            </el-form-item>
            <el-form-item label="参数字段" :label-width="formLabelWidth" prop="conf_key">
                <el-input size="medium" autocomplete="off" v-model="formAdd.conf_key"></el-input>
                <span style="color: #999;">参数字段，如：logo</span>
            </el-form-item>
            <el-form-item label="参数描述" :label-width="formLabelWidth">
                <el-input size="medium" autocomplete="off" v-model="formAdd.conf_desc"></el-input>
                <span style="color: #999;">参数描述，如：建议尺寸: 300*300</span>
            </el-form-item>
            <el-form-item label="字段类型" :label-width="formLabelWidth">
                <el-radio-group v-model="formAdd.conf_spec" size="small">
                    <el-radio-button label="0">文本框</el-radio-button>
                    <el-radio-button label="1">多行文本框</el-radio-button>
                    <el-radio-button label="2">单选框</el-radio-button>
                    <el-radio-button label="3">多选框</el-radio-button>
                    <el-radio-button label="4">单图</el-radio-button>
                    <el-radio-button label="5">多图</el-radio-button>
                    <el-radio-button label="6">富文本</el-radio-button>
                </el-radio-group>
                <el-input v-if="formAdd.conf_spec==2 || formAdd.conf_spec==3" type="textarea" v-model="formAdd.conf_content"
                    :rows="4" placeholder="参数方式例如:&#10;开启=>1&#10;关闭=>0" style="margin-top: 10px;"></el-input>
            </el-form-item>
            <el-form-item label="显示隐藏" :label-width="formLabelWidth">
                <el-switch size="medium" v-model="formAdd.conf_status"></el-switch>
            </el-form-item>
            <el-form-item label="排序" :label-width="formLabelWidth">
                <el-input-number v-model="formAdd.conf_sort" :min="0" :max="999" size="medium" style="width: 120px;" controls-position="right" />
            </el-form-item>
            <el-form-item label="系统参数" :label-width="formLabelWidth">
                <el-switch size="medium" v-model="formAdd.conf_system"></el-switch>
                <div style="color: #999;">开启后，添加的参数将无法删除</div>
            </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
            <el-button type="primary" @click="postAdd">确认添加</el-button>
        </div>
    </el-dialog>
    <!-- 修改框 -->
    <el-dialog title="修改配置" :visible.sync="dialogFormEdit" width="800px" :modal-append-to-body='false' append-to-body :close-on-click-modal='false'>
        <el-form :model="formEdit" status-icon :rules="rules" ref="formEdit">
            <el-form-item label="所属分类" :label-width="formLabelWidth" prop="conf_type">
                <el-select v-model="formEdit.conf_type" placeholder="请选择所属分类" size="medium">
                    <el-option label="基础设置" value="0"></el-option>
                    <el-option label="SEO设置" value="9"></el-option>
                    <el-option label="搜索设置" value="1"></el-option>
                    <el-option label="上传配置" value="2"></el-option>
                    <el-option label="前端模版" value="3"></el-option>
                    <el-option label="其他配置" value="4"></el-option>
                    <el-option label="微信支付配置" value="5"></el-option>
                    <el-option label="支付宝支付配置" value="6"></el-option>
                    <el-option label="微信小程序配置" value="7"></el-option>
                    <el-option label="微信公众号配置" value="8"></el-option>
                    <el-option label="交易设置" value="10"></el-option>
                    <el-option label="售后设置" value="11"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="参数名称" :label-width="formLabelWidth" prop="conf_title">
                <el-input size="medium" autocomplete="off" v-model="formEdit.conf_title"></el-input>
                <span style="color: #999;">参数名称，如：网站LOGO</span>
            </el-form-item>
            <el-form-item label="参数字段" :label-width="formLabelWidth" prop="conf_key">
                <el-input size="medium" autocomplete="off" v-model="formEdit.conf_key"></el-input>
                <span style="color: #6881ec;">参数字段，切勿随意修改</span>
            </el-form-item>
            <el-form-item label="参数描述" :label-width="formLabelWidth">
                <el-input size="medium" autocomplete="off" v-model="formEdit.conf_desc"></el-input>
                <span style="color: #999;">参数描述，如：建议尺寸: 300*300</span>
            </el-form-item>
            <el-form-item label="字段类型" :label-width="formLabelWidth">
                <el-radio-group v-model="formEdit.conf_spec" size="small">
                    <el-radio-button label="0">文本框</el-radio-button>
                    <el-radio-button label="1">多行文本框</el-radio-button>
                    <el-radio-button label="2">单选框</el-radio-button>
                    <el-radio-button label="3">多选框</el-radio-button>
                    <el-radio-button label="4">单图</el-radio-button>
                    <el-radio-button label="5">多图</el-radio-button>
                    <el-radio-button label="6">富文本</el-radio-button>
                    <el-radio-button label="7">颜色选择</el-radio-button>
                </el-radio-group>
                <el-input v-if="formEdit.conf_spec==2 || formEdit.conf_spec==3" type="textarea" v-model="formEdit.conf_content" :rows="4" placeholder="参数方式例如:&#10;开启=>1&#10;关闭=>0" style="margin-top: 10px;"></el-input>
            </el-form-item>
            <el-form-item label="显示隐藏" :label-width="formLabelWidth">
                <el-switch size="medium" v-model="formEdit.conf_status"></el-switch>
            </el-form-item>
            <el-form-item label="排序" :label-width="formLabelWidth">
                <el-input-number v-model="formEdit.conf_sort" :min="0" :max="999" size="medium" style="width: 120px;"
                    controls-position="right" />
            </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
            <el-button type="primary" @click="postEdit">确认修改</el-button>
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
                this.getList();
                return {
                    search: {
                        keyword: "",
                        filter: "conf_id"
                    },
                    formLabelWidth: '80px',
                    dialogFormAdd: false,
                    dialogFormEdit: false,
                    loading: true,
                    dataList: [],
                    selectList: [],
                    form: {
                        page: 1,
                        per_page: 10
                    },
                    formAdd: {
                        conf_status: true,
                        conf_spec: 0,
                        conf_sort: 0,
                    },
                    formEdit: {},
                    rules: {
                        conf_title: [
                            { required: true, message: '参数名称必须填写', trigger: 'blur' },
                        ],
                        conf_key: [
                            { required: true, message: '参数字段必须填写', trigger: 'blur' },
                        ],
                        conf_type: [
                            { required: true, message: '所属分类必须填写', trigger: 'blur' },
                        ],
                    }
                }
            },
            methods: {
                getList_search() {
                    this.form.page = 1;
                    this.getList();
                },
                handleSizeChange(per_page) {
                    this.form.per_page = per_page;
                    this.getList();
                },
                postMultDelete() {
                    var that = this;
                    if (that.selectList.length == 0) {
                        that.$message.error('未选择任何配置！');
                        return;
                    }
                    this.$confirm('即将删除选中的配置, 是否确认?', '批量删除', {
                        confirmButtonText: '删除',
                        cancelButtonText: '取消',
                        type: 'warning'
                    }).then(() => {
                        axios.post('/admin/conf/delete', Object.assign({}, PostBase, {
                            conf_id: that.selectList.join(",")
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
                                console.log(error);
                            });
                    }).catch(() => {
                    });
                },
                changeSelection(list) {
                    var that = this;
                    that.selectList = [];
                    for (var index in list) {
                        that.selectList.push(list[index].conf_id);
                    }
                },
                postEdit() {
                    var that = this;
                    that.$refs['formEdit'].validate((valid) => {
                        if (!valid) {
                            that.$message.error('仔细检查检查，是不是有个地方写得不对？');
                            return;
                        }
                        axios.post('/admin/conf/update', Object.assign({}, PostBase, that.formEdit))
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
                    });
                },
                postAdd() {
                    var that = this;
                    that.$refs['formAdd'].validate((valid) => {
                        if (!valid) {
                            that.$message.error('仔细检查检查，是不是有个地方写得不对？');
                            return;
                        }
                        axios.post('/admin/conf/add', Object.assign({}, PostBase, that.formAdd))
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
                                console.log(error);
                            });
                    });
                },
                clickAdd() {
                    var that = this;
                    that.formAdd = { 
                        conf_status: true, 
                        conf_spec: 0,
                        conf_sort: 0,
                    };
                    axios.post('/admin/conf/getList', Object.assign({}, PostBase))
                        .then(function (response) {
                            that.groupList = response.data.data;
                            if (response.data.code == CODE_SUCCESS) {
                                that.dialogFormAdd = true;
                            } else {
                                that.$message.error(response.data.message);
                            }
                        })
                        .catch(function (error) {
                            that.$message.error('服务器内部错误');
                            console.log(error);
                        });
                },
                clickDelete(row) {
                    var that = this;
                    this.$confirm('即将删除这个配置, 是否确认?', '删除提醒', {
                        confirmButtonText: '删除',
                        cancelButtonText: '取消',
                        type: 'warning'
                    }).then(() => {
                        axios.post('/admin/conf/delete', Object.assign({}, PostBase, {
                            conf_id: row.conf_id
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
                                console.log(error);
                            });
                    }).catch(() => {
                    });
                },
                clickStatus(row) {
                    var that = this;
                    axios.post(row.conf_status ? '/admin/conf/enable' : '/admin/conf/disable', Object.assign({}, PostBase, {
                        conf_id: row.conf_id
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
                            console.log(error);
                        });
                },
                clickEdit(row) {
                    var that = this;
                    that.formEdit = row;
                    axios.post('/admin/conf/getList', Object.assign({}, PostBase))
                        .then(function (response) {
                            if (response.data.code == CODE_SUCCESS) {
                                that.groupList = response.data.data;
                                axios.post('/admin/conf/detail', Object.assign({}, PostBase, {
                                    conf_id: row.conf_id
                                }))
                                    .then(function (response) {
                                        if (response.data.code == CODE_SUCCESS) {
                                            response.data.data.conf_status = response.data.data.conf_status?true:false;
                                            response.data.data.conf_type = response.data.data.conf_type.toString();
                                            that.formEdit = response.data.data;
                                            that.dialogFormEdit = true;
                                        } else {
                                            that.$message.error(response.data.message);
                                        }
                                    })
                                    .catch(function (error) {
                                        that.$message.error('服务器内部错误');
                                        console.log(error);
                                    });
                            } else {
                                that.$message.error(response.data.message);
                            }
                        })
                        .catch(function (error) {
                            that.$message.error('服务器内部错误');
                            console.log(error);
                        });

                },
                changeCurrentPage(page) {
                    this.form.page = page;
                    this.getList();
                },
                getList() {
                    var that = this;
                    that.loading = true;
                    axios.post('/admin/conf/getList', Object.assign({}, PostBase, that.form, that.search))
                        .then(function (response) {
                            that.loading = false;
                            if (response.data.code == CODE_SUCCESS) {
                                that.dataList = response.data.data;
                            } else {
                                that.$message.error(response.data.message);
                            }
                        })
                        .catch(function (error) {
                            that.loading = false;
                            that.$message.error('服务器内部错误');
                            console.log(error);
                        });
                }
            }
        })
    </script>


</html>