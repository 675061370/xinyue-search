<?php /*a:5:{s:79:"C:\Users\liu67\Desktop\test\xinyue-search\app\qfadmin\view\source\category.html";i:1726194335;s:77:"C:\Users\liu67\Desktop\test\xinyue-search\app\qfadmin\view\common\header.html";i:1712232785;s:75:"C:\Users\liu67\Desktop\test\xinyue-search\app\qfadmin\view\common\menu.html";i:1712232786;s:77:"C:\Users\liu67\Desktop\test\xinyue-search\app\qfadmin\view\common\footer.html";i:1712232785;s:78:"C:\Users\liu67\Desktop\test\xinyue-search\app\qfadmin\view\component\view.html";i:1712232786;}*/ ?>
<!DOCTYPE html>
<html>

<head>
    <title>资源分类</title>
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
        <el-form-item>
            <el-button icon="el-icon-warning-outline" size="small" @click="dayShow=true" plain>如何每日更新？</el-button>
        </el-form-item>
        <el-form-item>
            <el-button icon="el-icon-warning-outline" size="small" @click="allShow=true" plain>如何全部转存？</el-button>
        </el-form-item>
    </el-form>
    <el-table :data="dataList" @selection-change="changeSelection" v-loading="loading">
        <el-table-column type="selection" width="50">
        </el-table-column>
        <el-table-column prop="source_category_id" label="ID" width="60">
        </el-table-column>
        <el-table-column label="分类名称">
            <template slot-scope="scope">
                <img :src="scope.row.image" style="width: 30px;height:30px;vertical-align: middle;" v-if="scope.row.image" />
                <span style="vertical-align: middle;margin-left: 5px">{{scope.row.name}}</span>
            </template>
        </el-table-column>
        <el-table-column label="是否每日更新" width="150" align="center">
            <template slot-scope="scope">
                <el-switch v-model="scope.row.is_update==1?true:false"
                    @change="clickStatus(scope.row)">
                </el-switch>
            </template>
        </el-table-column>
        <el-table-column label="是否前台展示" width="150" align="center">
            <template slot-scope="scope">
                <el-switch v-model="scope.row.status==1?false:true"
                    @change="clickStatus(scope.row)">
                </el-switch>
            </template>
        </el-table-column>
        <el-table-column prop="sort" label="排序"  width="150" align="center">
        </el-table-column>
        <el-table-column label="操作" width="200">
            <template slot-scope="scope">
                <el-link type="primary" @click="s2Btn(scope.row)" :underline="false">一键转存</el-link>&nbsp;&nbsp;&nbsp;&nbsp;
                <el-link type="primary" @click="clickEdit(scope.row)" :underline="false">编辑</el-link>&nbsp;&nbsp;&nbsp;&nbsp;
                <el-link type="danger" @click="clickDelete(scope.row)" :underline="false">删除</el-link>
            </template>
        </el-table-column>
    </el-table>


    <!-- 添加框 -->
    <el-dialog title="添加资源分类名称" :visible.sync="dialogFormAdd" width="500px" :modal-append-to-body='false' append-to-body :close-on-click-modal='false'>
        <el-form :model="formAdd" :rules="rules" ref="formAdd">
            
            <el-form-item prop="name" label="分类名称" :label-width="formLabelWidth">
                <el-input size="medium" autocomplete="off" v-model="formAdd.name"></el-input>
            </el-form-item>
            
            <el-form-item prop="image" label="图标" :label-width="formLabelWidth">
                <Single v-model="formAdd.image"></Single>
            </el-form-item>
            <el-form-item prop="sort" label="排序" :label-width="formLabelWidth">
                <el-input-number v-model="formAdd.sort" :min="0" :max="999" size="medium" style="width: 120px;"
                    controls-position="right" />
            </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
            <el-button type="primary" @click="postAdd()">确认添加</el-button>
        </div>
    </el-dialog>
    <!-- 修改框 -->
    <el-dialog title="修改资源分类信息" :visible.sync="dialogFormEdit" width="500px" :modal-append-to-body='false' append-to-body :close-on-click-modal='false'>
        <el-form :model="formEdit" :rules="rules" ref="formEdit">
            <el-form-item prop="name" label="分类名称" :label-width="formLabelWidth">
                <el-input size="medium" autocomplete="off" v-model="formEdit.name" disabled ></el-input>
            </el-form-item>
            <el-form-item prop="image" label="图标" :label-width="formLabelWidth">
                <Single v-model="formEdit.image"></Single>
            </el-form-item>
            <el-form-item prop="sort" label="排序" :label-width="formLabelWidth">
                <el-input-number v-model="formEdit.sort" :min="0" :max="999" size="medium" style="width: 120px;"
                    controls-position="right" />
            </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
            <el-button type="primary" @click="postEdit()">确认修改</el-button>
        </div>
    </el-dialog>
     <!-- 每日更新 -->
    <el-dialog title="每日更新" :visible.sync="dayShow" width="500px" :modal-append-to-body='false' append-to-body :close-on-click-modal='false'>
        <div style="margin-bottom: 20px;font-size:14px;color:#666;line-height: 2">
            <p>自动更新：<font color=orangered>转存当日及昨天的资源数据；</font></p>
            <font color=orangered>将此接口添加到计划任务中，计划任务每日执行一次即可；</font>
            <p>接口地址：{{domain}}/api/source/day</p>
            <p>Tips：添加计划任务后方可生效；名称重复的资源会跳过转存；</p>
            <p>宝塔任务类型：访问URL-GET</p>
        </div>
    </el-dialog>
    
     <!-- 全部更新 -->
    <el-dialog title="全部转存" :visible.sync="allShow" width="500px" :modal-append-to-body='false' append-to-body :close-on-click-modal='false'>
        <div style="margin-bottom: 20px;font-size:14px;color:#666;line-height: 2">
            <p>全部转存：<font color=orangered>一键转存心悦搜索所有资源到自己的网盘及系统中</font></p>
            <font color=orangered>全部转存速度比较慢，提交后请耐心等待；名称重复的资源会跳过转存；</font>
            <p>全部转存需要按类别转存：见下方列表一键转存按钮</p>
            <p>Tips：心悦搜索：https://pan.xinyuedh.com；</p>
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
                    formLabelWidth: '80px',
                    dialogFormAdd: false,
                    dialogFormEdit: false,
                    loading: true,
                    dataList: [],
                    selectList: [],
                    formAdd: {
                        image: '',
                        sort: 0
                    },
                    formEdit: {
                    },
                    rules: {
                        name: [ { required: true, message: '请输入分类名称', trigger: 'blur' }],
                    },
                    
                    allShow: false,
                    dayShow: false,
                    domain: window.location.protocol+'//'+window.location.host
                }
            },
            methods: {
                changeSelection(list) {
                    var that = this;
                    that.selectList = [];
                    for (var index in list) {
                        that.selectList.push(list[index].source_category_id);
                    }
                },
                postEdit() {
                    var that = this;
                    that.$refs["formEdit"].validate((valid) => {
                        if (valid) {
                            axios.post('/admin/source_category/update', Object.assign({}, PostBase, that.formEdit))
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
                            axios.post('/admin/source_category/add', Object.assign({}, PostBase, that.formAdd))
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
                        }
                    });
                },
                clickAdd() {
                    var that = this;
                    that.formAdd = { sort: 0,image: '' };
                    that.dialogFormAdd = true;
                },
                clickDelete(row) {
                    var that = this;
                    this.$confirm('即将删除这个分类, 是否确认?', '删除提醒', {
                        confirmButtonText: '删除',
                        cancelButtonText: '取消',
                        type: 'warning'
                    }).then(() => {
                        axios.post('/admin/source_category/delete', Object.assign({}, PostBase, {
                            source_category_id: row.source_category_id
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
                    axios.post(row.status ? '/admin/source_category/enable' : '/admin/source_category/disable', Object.assign({}, PostBase, {
                        source_category_id: row.source_category_id
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
                    axios.post('/admin/source_category/detail', Object.assign({}, PostBase, {
                        source_category_id: row.source_category_id
                    }))
                        .then(function (response) {
                            if (response.data.code == CODE_SUCCESS) {
                                that.formEdit = response.data.data;
                                that.formEdit.image = that.formEdit.image||''
                                that.dialogFormEdit = true;
                            } else {
                                that.$message.error(response.data.message);
                            }
                        })
                        .catch(function (error) {
                            that.$message.error('服务器内部错误');
console.log(error);
                        });

                },
                getList() {
                    var that = this;
                    that.loading = true;
                    axios.post('/admin/source_category/getList', Object.assign({}, PostBase))
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
                },
                

                s2Btn(row){
                    let that = this
                    if(row.source_category_id!=1) return that.$message.error('当前版本仅支持短剧');
                    this.$confirm('全部转存速度比较慢，提交后请耐心等待,该操作不可暂停, 是否继续?', '提示', {
                        confirmButtonText: '确定',
                        cancelButtonText: '取消',
                        type: 'warning'
                    }).then(() => {
                        axios.post('/admin/source/transferAll', Object.assign({}, PostBase,{
                            source_category_id: row.source_category_id
                        }))
                            .then(function (res) {
                                if (res.data.code == 200) {
                                } else {
                                    that.$message.error(res.data.message);
                                }
                            })
                            .catch(function (error) {
                            });
                        that.$message({
                            message: "已提交任务，稍后查看结果",
                            type: 'success'
                        });
                    }).catch(() => {});
                    
                },
            }
        })
    </script>


</html>