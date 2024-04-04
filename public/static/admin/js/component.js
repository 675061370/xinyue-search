/**
 * @description: 图片库 
 * @param {*}
 * @return {*}
 */
Vue.component("Uploads", {
    template: '#Upload',
    data() {
        return {
            fullscreenLoading: false,
            visible: false,
            loading: true,
            form: {
                page: 1,
                per_page: 18
            },
            dataList: [],
            fileList: [],
            selectList: [],
            postData: PostBase,
            multiple: false,  //是否支持多选
            selected_num: 0,  //已选择数量
            total_num: 10,  //总共可选择数量
        }
    },
    methods: {
        save(){
            this.visible = false
            this.show('', this.selectList,true)
        },
        show(call,data,type=false){
            if (type) return this.call(data);
            this.call = call;
            this.visible = true
            this.multiple = data?data.multiple:0
            this.selected_num = data ?data.selected_num:0
            this.total_num = data ?data.total_num:10
            this.selectList = []
            this.getList()
        },
        select(index){
            if(this.multiple){
                if (this.dataList.data[index].select) {
                    this.dataList.data[index].select = false
                    for (let i = 0; i < this.selectList.length; i++) {
                        if (this.selectList[i].url == this.dataList.data[index].attach_path) {
                            this.selectList.splice(i, 1);
                        }
                    }
                } else {
                    if(this.selectList.length + this.selected_num >= this.total_num){
                        return this.$message.error('最多可选择'+ this.total_num+'文件');
                    }
                    this.dataList.data[index].select = true
                    this.selectList.push({
                        name: this.dataList.data[index].attach_name,
                        url: this.dataList.data[index].attach_path
                    })
                }
            }else{
                this.selectList = [];
                for (let item of this.dataList.data) {
                    item.select = false
                }
                this.dataList.data[index].select = true
                this.selectList.push({
                    name: this.dataList.data[index].attach_name,
                    url: this.dataList.data[index].attach_path
                })
            }
        },
        changeCurrentPage(page) {
            this.form.page = page;
            this.getList();
        },
        getList() {
            var that = this;
            that.loading = true;
            axios.post('/admin/attach/getList', Object.assign({}, PostBase, that.form))
                .then(function (res) {
                    that.loading = false;
                    if (res.data.code == 200) {
                        for (let item of res.data.data.data) {
                            item.select = false
                            for (let items of that.selectList) {
                                if(item.attach_path == items.url){
                                    item.select = true
                                }
                            }
                        }
                        that.dataList = res.data.data;
                    } else {
                        that.$message.error(res.data.message);
                    }
                })
                .catch(function (error) {
                    that.loading = false;
                    that.$message.error('服务器内部错误');
                    console.log(error);
                });
        },
        handleUploadSuccess(res, file) {
            this.fullscreenLoading = false;
            if (res.code == 200) {
                this.$message({
                    message: res.message,
                    type: 'success'
                });
                this.form.page = 1;
                this.getList();
            } else {
                this.$message.error(res.message);
            }
        },
        beforeUpload(file) {
            const isImage = file.type === 'image/jpeg' || file.type === 'image/png';
            const isLt2M = file.size / 1024 / 1024 < 2;
            if (!isImage) {
                this.$message.error('上传图片只能是 JPG/PNG 格式!');
            }
            if (!isLt2M) {
                this.$message.error('上传头像图片大小不能超过 2MB!');
            }
            this.fullscreenLoading = true;
            return isImage && isLt2M;
        },
    }
});
/**
 * @description: 富文本
 * @param {*}
 * @return {*}
 */
Vue.component('Ueditors', VueUeditorWrap);
Vue.component("Ueditor", {
    template: '#Ueditor',
    // 接收父组件传递过来的参数
    props: {
        value: {
            type: String,
            default: ''
        },
        index:{ 
            type: Number,
            default: 1
        },
    },
    data() {
        return {
            config: {
                // 编辑器层级的基数,默认是900
                zIndex: 9000,
                // 编辑器自动被内容撑高
                autoHeightEnabled: true,
                // 初始容器高度
                initialFrameHeight: 280,
                // 初始容器宽度
                initialFrameWidth: "100%",
                // 上传文件接口（这个地址是我为了方便各位体验文件上传功能搭建的临时接口，请勿在生产环境使用！！！部署在国外的服务器，如果无法访问，请自备梯子）
                serverUrl: "/admin/attach/uploads",
                // 给编辑区域的iframe引入一个css文件
                // iframeCssUrl: "static/UEditor/themes/iframe.css",
                // 图片操作的浮层开关
                imagePopup: false,
                // 打开右键菜单功能
                enableContextMenu: false,
                // 是否保持toolbar的位置不动,默认true
                autoFloatEnabled: false,
                // 工具栏上的所有的功能按钮和下拉框
                toolbars: [[
                    'source', '|', 'undo', 'redo', '|',
                    'bold', 'italic', 'underline', 'strikethrough', 'removeformat',
                    // 纯文本粘贴
                    'pasteplain', '|', 'forecolor', 'backcolor', 'selectall', 'cleardoc', '|',
                    'rowspacingtop', 'rowspacingbottom', 'lineheight', '|',
                    'fontsize', '|',
                    'indent', 'justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', '|', 'touppercase', 'tolowercase', '|',
                    'simpleupload',
                    'insertimage',
                    // 'insertvideo'
                ]]
            },
            content: '',
            list: [],
        }
    },
    watch: {
        value(newVal) {
            this.$emit('input', newVal)
        }
    },
    methods: {
        // 注册自定义组件
        addCustomButtom(editorId) {
            this.setbutton(editorId)
        },
        setbutton(editorId) {
            let _this = this
            window.UE.registerUI('simpleupload', function (editor, uiName) {
                // 创建一个 button
                var btn = new window.UE.ui.Button({
                    // 按钮的名字
                    name: uiName,
                    // 提示
                    title: '插入图片',
                    // 需要添加的额外样式，可指定 icon 图标，图标路径参考常见问题 2
                    cssRules: '',
                    // 点击时执行的命令
                    onclick: () => {
                        // 打开文件选择器
                        app.$refs.upload.show((e) => {
                            if (e.length > 0) {
                                let content = e.map(item => {
                                    return `<p><img src="${item.url}"/></p>`
                                })
                                _this.inserthtml(content.join(''))
                            }
                        }, {
                            multiple: true
                        }, false);
                    }
                })
                // 因为你是添加 button，所以需要返回这个 button
                return btn
            }, -1 /* 指定添加到工具栏上的哪个位置，默认时追加到最后 */ ,editorId /* 指定这个 UI 是哪个编辑器实例上的，默认是页面上所有的编辑器都会添加这个按钮 */)
        },
        inserthtml (content) {
            let editor = this.$refs.Ueditor.editor
            editor.execCommand('inserthtml', content)
        }
    }
});

/**
 * @description: 图片选择 
 * @param {*}
 * @return {*}
 */
Vue.component("Single", {
    template: '#Single',
    data() {
        return {
            drag:false,
        }
    },
    // 接收父组件传递过来的参数
    props: {
        value: {
            type: String
        },
        multiple:{  //是否支持多图选择
            type: Boolean,
            default: false
        },
        selected_num:{ //已选择的数量
            type: Number,
            default: 0
        },
        total_num:{ //总共可选择数量
            type: Number,
            default: 10
        },
    },
    methods: {
        selectimg() {
            if (this.multiple) {
                app.$refs.upload.show((e) =>{
                    for (let item of e) {
                        this.value.push(item.url)
                        this.$emit('input', this.value);
                    }
                }, {
                    multiple: true,
                    selected_num: this.selected_num,
                    total_num: this.total_num,
                }, false);
            } else {
                app.$refs.upload.show((e) =>{
                    this.$emit('input', e[0].url);
                }, {}, false);
            }
        },
        deles(s) {
            if (this.multiple) {
                this.value.splice(s,1)
                this.$emit('input', this.value);
            } else {
                this.$emit('input', '');
            }
        },
        //开始拖拽事件
        onStart(){
            this.drag = true;
        },
        //拖拽结束事件
        onEnd() {
            this.drag = false;
            this.$emit('input', this.value);
        },
    }
});


/**
 * @description: SKU 
 * @param {*}
 * @return {*}
 */
SkuForm.default.install(Vue);
Vue.component("vue-sku", {
    template: '#skuforms',
    // 接收父组件传递过来的参数
    props: {
    },
    data() {
        return {
            inputVisible: false,
            inputValue: '',
            sourceAttribute: [],
            structure: [
                {
                    name: 'goods_price',
                    type: 'input',
                    label: '商品价格',
                    required: true,
                    validate: (data, index, callback) => {
                        if (parseFloat(data[index].goods_price) < 0.01) callback(new Error('商品价格不能小于0.01'))
                        if (isNaN(data[index].goods_price)) callback(new Error('请输入正确的商品价格'))
                        callback()
                    }
                },
                {
                    name: 'stock',
                    type: 'input',
                    label: '库存',
                    required: true,
                    validate: (data, index, callback) => {
                        if ( parseInt(data[index].stock) < 0) callback(new Error('库存不能小于0'))
                        if (isNaN(data[index].stock)) callback(new Error('请输入正确的库存'))
                        callback()
                    }
                },
                {
                    name: 'line_price',
                    type: 'input',
                    label: '划线价格',
                    validate: (data, index, callback) => {
                        if (isNaN(data[index].line_price)) callback(new Error('请输入正确的划线价格'))
                        callback()
                    }
                },
                {
                    name: 'goods_weight',
                    type: 'input',
                    label: '商品重量',
                    validate: (data, index, callback) => {
                        if (isNaN(data[index].goods_weight)) callback(new Error('请输入正确的商品重量'))
                        callback()
                    }
                },
            ],
			attribute: [],
            sku: [],
        }
    },
    methods: {
        showInput() {
            this.inputVisible = true;
            this.$nextTick(_ => {
                this.$refs.saveTagInput.$refs.input.focus();
            });
        },

        handleInputConfirm() {
            let inputValue = this.inputValue;
            this.sourceAttribute.push(
                {
                    name: inputValue,
					item: []
                }
            );
            this.$refs.skuForm.init()
            this.inputVisible = false;
            this.inputValue = '';
        },

        setdata(sku, attribute) {
            setTimeout(() => {
                this.sourceAttribute = attribute
                setTimeout(() => {
                    this.attribute = attribute
                    setTimeout(() => {
                        this.sku = sku
                        // 切记，必须在 attribute、sku 数据都加载后再执行 init() 方法
                        this.$refs.skuForm.init()
                    }, 100)
                }, 100)
            }, 100)
        },
    },
});

/**
 * @description: 商品库 
 * @param {*}
 * @return {*} 
 */
Vue.component("Goodslists", {
    template: '#Goodslist',
    data() {
        return {
            fullscreenLoading: false,
            visible: false,
            loading: true,
            form: {
                page: 1,
                per_page: 5
            },
            search: {
                keyword: "",
                filter: "goods_name",
                classify: '',
            },
            dataList: [],
            dataList2: [],
            selectList: [],
            categoryList: [],
            postData: PostBase,
            cascaderProps: {
                value: 'goods_category_id',
                label: 'name',
                children: 'children',
                checkStrictly: true,
                emitPath: false
            },
        }
    },
    methods: {
        save(){
            this.visible = false
            let data = this.unique2(this.selectList.concat(this.selectList2))
            this.$refs.multipleTable.clearSelection();
            this.show('', data, true)
        },
        show(call, data=[], type = false) {
            if (type) return this.call(data);
            this.call = call;
            this.visible = true
            this.selectList2 = JSON.parse(JSON.stringify(data));
            this.init()
        },
        handleClose(done) {
            this.$refs.multipleTable.clearSelection();
            done();
        },
        cancels() {
            this.$refs.multipleTable.clearSelection();
            this.visible = false
        },
        init() {
            this.form = {
                page: 1,
                per_page: 5
            }
            this.search = {
                keyword: "",
                filter: "goods_name",
                classify: '',
            }
            this.getcategory()
        },

        getList_search(val) {
            if(val==0){
                this.search = {
                    keyword: "",
                    filter: "goods_name",
                    classify: '',
                }
            }
            this.form.page = 1;
            this.getList();
        },
        //获取商品分类
        getcategory() {
            var that = this;
            that.loading = true;
            axios.post('/admin/goodsCategory/getList', Object.assign({}, PostBase))
                .then(function (response) {
                    if (response.data.code == CODE_SUCCESS) {
                        that.getList();
                        that.categoryList = response.data.data;
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
            axios.post('/admin/goods/getList', Object.assign({}, PostBase, that.form, that.search, {
                tabPane: 'sale'
            }))
                .then(function (res) {
                    that.loading = false;
                    if (res.data.code == 200) {
                        that.dataList = res.data.data;
                        for (let item of that.dataList.data) {
                            for (let i = 0; i < that.selectList2.length; i++) {
                                if (item.goods_id == that.selectList2[i]) {
                                    if (that.selectList.indexOf(item.goods_id) == -1) {
                                        that.$nextTick(() => {
                                            that.$refs.multipleTable.toggleRowSelection(item, true);
                                        })
                                    }
                                    that.selectList2.splice(i, 1);
                                }
                            }
                        }
                    } else {
                        that.$message.error(res.data.message);
                    }
                })
                .catch(function (error) {
                    that.loading = false;
                    that.$message.error('服务器内部错误');
                });
        },
        changeSelection(list) {
            var that = this;
            that.unique(list)
            that.selectList = [];
            for (var index in list) {
                that.selectList.push(list[index].goods_id);
            }
        },
        changeCurrentPage(page) {
            this.form.page = page;
            this.getList();
        },

        unique(arr){            
            for(var i=0; i<arr.length; i++){
                for(var j=i+1; j<arr.length; j++){
                    if(arr[i].goods_id==arr[j].goods_id){         //第一个等同于第二个，splice方法删除第二个
                        arr.splice(j,1);
                        j--;
                    }
                }
            }
            return arr;
        },
        unique2(arr){            
            for(var i=0; i<arr.length; i++){
                for(var j=i+1; j<arr.length; j++){
                    if(arr[i]==arr[j]){         //第一个等同于第二个，splice方法删除第二个
                        arr.splice(j,1);
                        j--;
                    }
                }
            }
            return arr;
        }
    }
});