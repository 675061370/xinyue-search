<?php /*a:1:{s:75:"C:\Users\liu67\Desktop\test\xinyue-search\app\qfadmin\view\admin\login.html";i:1712232785;}*/ ?>
<!DOCTYPE html>
<html>

<head>
    <title>后台管理系统</title>
    <meta charset="UTF-8">
    <!-- import CSS -->
    <link rel="stylesheet" href="/static/admin/css/element.css">
    <link rel="stylesheet" href="/static/admin/css/YAdmin.css">
    <style>
        .login-container {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            display: flex;
            align-items: center;
            justify-content: space-around;
            min-width: 1280px;
            min-height: 800px;
            background: url(/static/admin/images/login_bg.jpg) no-repeat;
            background-size: 100% 100%;
            background-color: #ffffff;
        }
        .login-container:before{
            content: " ";
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: url(/static/admin/images/login_bg_bottom.png) no-repeat;
            background-size: 100% 100%;
            height: 170px;
        }
        .login-box img{
            width: 720px;
        }
        .login-border {
            display: flex;
            justify-content: center;
            flex-direction: column;
            position: relative;
            top: -30px;
            right: 100px;
        }
        .login-main{
            background-color: #ffffff;
            padding: 35px 45px 15px 45px;
            border-radius: 6px;
        }
        .login-main .vadmin{
            font-size: 16px;
            font-weight: bold;
            color: #6881ec;
            line-height: 20px;
            padding-bottom: 6px;
        }
        .login-logo {
            margin: 0 0 20px;
        }
        .login-logo p {
            color: #ffffff;
            font-size: 25px;
            font-weight: bold;
        }
        .login-submit {
            margin-top: 10px;
            width: 100%;
        }
        .login-form {
            margin: 10px 0;
        }
        .login-form .el-form-item__content {
            width: 270px;
        }
        .login-form .el-form-item {
            margin-bottom: 26px;
        }
        .login-form .el-input input {
            text-indent: 5px;
            border-color: #DCDCDC;
            border-radius: 3px;
            border: none;
            border-bottom: 1px solid #eee;
        }
        .login-form .el-input .el-input__prefix i {
            padding: 0 5px;
            font-size: 16px !important;
        }
        .login-code {
            display: flex;
            align-items: center;
            justify-content: space-around;
            margin-left: 10px;
            cursor: pointer;
        }
        .login-code-img {
            margin-top: 1px;
            width: 100px;
            height: 38px;
        }
    </style>
</head>

<body>
    <div id="app" v-cloak>
        <div id="app" v-cloak>
            <div class="login-container">
                <div class="login-box">
                    <img src="/static/admin/images/login_bg_box.png">
                </div>
                <div class="login-border">
                    <div class="login-logo">
                        <p>后台登录</p>
                    </div>
                    <div class="login-main">
                        <p class="vadmin">管理员登录</p>
                        <el-form class="login-form" status-icon :rules="loginRules" ref="loginForm" :model="loginForm"
                            label-width="0" size="default">
                            <el-form-item prop="admin_account">
                                <el-input @keyup.enter.native="handleLogin()" v-model="loginForm.admin_account"
                                    auto-complete="off" placeholder="请输入账号">
                                    <i slot="prefix" class="el-icon-user"></i>
                                </el-input>
                            </el-form-item>
                            <el-form-item prop="admin_password">
                                <el-input @keyup.enter.native="handleLogin()" v-model="loginForm.admin_password" show-password
                                    auto-complete="off" placeholder="请输入密码">
                                    <i slot="prefix" class="el-icon-key"></i>
                                </el-input>
                            </el-form-item>
                            <el-form-item v-if="codeUrl" prop="admin_code">
                                <el-row :span="34">
                                    <el-col :span="14">
                                        <el-input @keyup.enter.native="handleLogin()" v-model="loginForm.admin_code"
                                            auto-complete="off" placeholder="请输入验证码">
                                            <i slot="prefix" class="el-icon-mobile"></i>
                                        </el-input>
                                    </el-col>
                                    <el-col :span="10">
                                        <div class="login-code">
                                            <img :src="codeUrl" class="login-code-img" @click="getCaptcha" alt="" />
                                        </div>
                                    </el-col>
                                </el-row>
                            </el-form-item>
                            <el-form-item>
                                <el-button type="primary" :loading="loading" @click.native.prevent="handleLogin"
                                    class="login-submit">登 录
                                </el-button>
                            </el-form-item>
                        </el-form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<script src="/static/admin/js/vue-2.6.10.min.js"></script>
<script src="/static/admin/js/axios.min.js"></script>
<script src="/static/admin/js/element.js"></script>
<script src="/static/admin/js/YAdmin.js"></script>
<script>
    new Vue({
        el: '#app',
        data() {
            return {
                codeUrl: '',
                codeToken: '',
                loading: false,
                loginForm: {
                    admin_account: '',
                    admin_password: '',
                    admin_code: '',
                },
                loginRules: {
                    admin_account: [
                        { required: true, message: '请输入账号', trigger: 'blur' }
                    ],
                    admin_password: [
                        { required: true, message: '请输入密码', trigger: 'blur' },
                        { min: 6, message: '密码长度最少为6位', trigger: 'blur' }
                    ],
                    admin_code: [
                        { required: true, message: '请输入验证码', trigger: 'blur' },
                        { min: 4, max: 4, message: '验证码长度为4位', trigger: 'blur' }
                    ]
                }
            }
        },
        created() {
            this.getCaptcha();
        },
        methods: {
            /**
             * @description 正式登录
             */
            handleLogin() {
                var that = this;
                this.$refs.loginForm.validate(valid => {
                    if (valid) {
                        this.loading = true
                        this.loginForm.token = this.codeToken
                        axios.post('/admin/admin/login', Object.assign({}, PostBase, this.loginForm))
                            .then(function (response) {
                                if (response.data.code == CODE_SUCCESS) {
                                    that.$message({
                                        message: '登录成功,正在跳转中',
                                        type: 'success'
                                    });
                                    setTimeout(function () {
                                        location.replace('/qfadmin');
                                    }, 1000)
                                } else {
                                    that.$message.error(response.data.message);
                                    that.loading = false
                                    that.getCaptcha();
                                }
                            })
                            .catch(function (error) {
                                that.$message.error('登录失败，服务器内部错误');
                                that.loading = false
                            });
                    }
                })
            },
            onSubmit() {
                var that = this;
                axios.post('/admin/admin/login', Object.assign({}, PostBase, this.form))
                    .then(function (response) {
                        if (response.data.code == CODE_SUCCESS) {
                            that.$message({
                                message: '登录成功,正在跳转中',
                                type: 'success'
                            });
                            setTimeout(function () {
                                location.replace('<?php echo htmlentities($callback); ?>');
                            }, 1000)
                        } else {
                            that.$message.error(response.data.message);
                        }
                    })
                    .catch(function (error) {
                        that.$message.error('登录失败，服务器内部错误');
                    });
            },
            getCaptcha() {
                var that = this;
                axios.post('/admin/system/getCaptcha', Object.assign({}, PostBase))
                    .then(function (res) {
                        that.codeUrl = res.data.data.img
                        that.codeToken = res.data.data.token
                    })
                    .catch(function (error) {
                        that.$message.error('获取失败');
                    });
            },
        }
    })
</script>

</html>