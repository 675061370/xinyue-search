var CODE_SUCCESS = 200;
var access_token = getCookie("access_token");
var PostBase = {
    access_token: access_token,
    plat: "admin",
    version: "10000"
};
function logout() { 
    var that = this;
    var r=confirm("是否确认安全退出？");
    if (r==true){
        axios.post('/admin/admin/logout', Object.assign({}, PostBase))
            .then(function (res) {
                if (res.data.code == 200) {
                    window.location.href = "/qfadmin"
                } else { 
                    alert(res.data.message)
                }
            })
            .catch(function (error) {
            });
    }
}
//写cookies
function setCookie(name, value) {
    var Days = 30;
    var exp = new Date();
    exp.setTime(exp.getTime() + Days * 24 * 60 * 60 * 1000);
    document.cookie = name + "=" + escape(value) + ";expires=" + exp.toGMTString();
}
//读取cookies
function getCookie(name) {
    var arr, reg = new RegExp("(^| )" + name + "=([^;]*)(;|$)");

    if (arr = document.cookie.match(reg))

        return unescape(arr[2]);
    else
        return null;
}
//删除cookies
function delCookie(name) {
    var exp = new Date();
    exp.setTime(exp.getTime() - 1);
    var cval = getCookie(name);
    if (cval != null)
        document.cookie = name + "=" + cval + ";expires=" + exp.toGMTString();
}

const isDebug = true;
function echo(message, color = "#000", fontSize = "14") {
    if (isDebug) {
        console.log("%c " + message, "font-size:" + fontSize + "px;color:" + color + ";font-weight:bold;");
    }
}

function echoGroupStart(message, color = "#000") {
    if (isDebug) {
        console.group("%c " + message, "font-size:16px;color:" + color + ";font-weight:bold;");
    }
}
function echoGroupEnd(message, color = "#000") {
    if (isDebug) {
        console.groupEnd();
        console.log("%c " + message, "font-size:16px;color:" + color + ";font-weight:bold;");
    }
}

function isChinese(obj) {
    var reg = /^[\u0391-\uFFE5]+$/;
    if (obj != "" && !reg.test(obj)) {
        return false;
    } else {
        return true;
    }
}

function checkZm(zm) {
    var zmReg = /^[a-zA-Z]*$/;
    if (zm != "" && !zmReg.test(zm)) {
        return false;
    } else {
        return true;
    }
}

function checkInt(obj) {
    var reg = /^[0-9]+$/;
    if (obj != "" && !reg.test(obj)) {
        return false;
    } else {
        return true;
    }
}

function checkZmOrInt(zmnum) {
    var zmnumReg = /^[0-9a-zA-Z]*$/;
    if (zmnum != "" && !zmnumReg.test(zmnum)) {
        return false;
    } else {
        return true;
    }
}

function checkEmail(obj) {
    //对电子邮件的验证
    var myreg = /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
    if (!myreg.test(obj)) {
        return false;
    } else {
        return true;
    }
}

function checkMobile(mobile) {
    if (mobile.length == 0) {
        return false;
    }
    if (mobile.length != 11) {
        return false;
    }

    if (!(/^1[34578]\d{9}$/.test(mobile))) {
        return false;
    } else {
        return true;
    }
}

function checkIDCard(idCard) {
    var checkFlag = new IDCard(idCard);
    if (!checkFlag.IsValid()) {
        return false;
    } else {
        return true;
    }
}

function checkDouble(value) {
    var reg = /^(-?\d+).(-?\d+)?$/
    if (reg.test(value)) {
        return true;
    } else {
        return false;
    }
}

function checkDateTime(value) {
    var reg = /^(?:19|20)[0-9][0-9]-(?:(?:0[1-9])|(?:1[0-2]))-(?:(?:[0-2][1-9])|(?:[1-3][0-1])) (?:(?:[0-2][0-3])|(?:[0-1][0-9])):[0-5][0-9]:[0-5][0-9]$/;
    if (reg.test(value)) {
        return true;
    } else {
        return false;
    }
}

function checkTime(value, type = "H:i:s") {
    var reg = /^((?:[0-2][0-3])|(?:[0-1][0-9])|(?:[0-9])):([0-5][0-9]|[0-9]):([0-5][0-9]|[0-9])$/;
    switch (type) {
        case 'H:i:s':
            reg = /^((?:[0-2][0-3])|(?:[0-1][0-9])|(?:[0-9])):([0-5][0-9]|[0-9]):([0-5][0-9]|[0-9])$/;
            break;
        case 'H:i':
            reg = /^((?:[0-2][0-3])|(?:[0-1][0-9])|(?:[0-9])):([0-5][0-9]|[0-9])$/;
            break;
        default:
    }
    if (reg.test(value)) {
        return true;
    } else {
        return false;
    }
}

function checkDate(value, type = "Y-m-d") {
    var reg = /^(?:19|20)[0-9][0-9]-(?:(?:[1-9])|(?:0[1-9])|(?:1[0-2]))-(?:(?:[1-9])|(?:[0-2][1-9])|(?:[1-3][0-1]))$/;
    switch (type) {
        case 'Y-m-d':
            reg = /^(?:19|20)[0-9][0-9]-(?:(?:[1-9])|(?:0[1-9])|(?:1[0-2]))-(?:(?:[1-9])|(?:[0-2][1-9])|(?:[1-3][0-1]))$/;
            break;
        case 'm-d':
            reg = /^(?:(?:[1-9])|(?:0[1-9])|(?:1[0-2]))-(?:(?:[1-9])|(?:[0-2][1-9])|(?:[1-3][0-1]))$/;
            break;
        default:
    }
    if (reg.test(value)) {
        return true;
    } else {
        return false;
    }
}

function checkTrainNo(value) {
    var reg = /^(C|D|G|Z|T|K|L)([0-9][0-9]|[0-9][0-9][0-9]|[0-9][0-9][0-9][0-9])$/;
    if (reg.test(value.toUpperCase())) {
        return true;
    } else {
        return false;
    }
}

function IDCard(CardNo) {
    this.Valid = false;
    this.ID15 = '';
    this.ID18 = '';
    this.Local = '';
    if (CardNo != null) this.SetCardNo(CardNo);
}

// 设置身份证号码，15位或者18位
IDCard.prototype.SetCardNo = function (CardNo) {
    this.ID15 = '';
    this.ID18 = '';
    this.Local = '';
    CardNo = CardNo.replace(" ", "");
    var strCardNo;
    if (CardNo.length == 18) {
        pattern = /^\d{17}(\d|x|X)$/;
        if (pattern.exec(CardNo) == null) return;
        strCardNo = CardNo.toUpperCase();
    } else {
        pattern = /^\d{15}$/;
        if (pattern.exec(CardNo) == null) return;
        strCardNo = CardNo.substr(0, 6) + '19' + CardNo.substr(6, 9)
        strCardNo += this.GetVCode(strCardNo);
    }
    this.Valid = this.CheckValid(strCardNo);
}

// 校验身份证有效性
IDCard.prototype.IsValid = function () {
    return this.Valid;
}

// 返回生日字符串，格式如下，1981-10-10
IDCard.prototype.GetBirthDate = function () {
    var BirthDate = '';
    if (this.Valid) BirthDate = this.GetBirthYear() + '-' + this.GetBirthMonth() + '-' + this.GetBirthDay();
    return BirthDate;
}

// 返回生日中的年，格式如下，1981
IDCard.prototype.GetBirthYear = function () {
    var BirthYear = '';
    if (this.Valid) BirthYear = this.ID18.substr(6, 4);
    return BirthYear;
}

// 返回生日中的月，格式如下，10
IDCard.prototype.GetBirthMonth = function () {
    var BirthMonth = '';
    if (this.Valid) BirthMonth = this.ID18.substr(10, 2);
    if (BirthMonth.charAt(0) == '0') BirthMonth = BirthMonth.charAt(1);
    return BirthMonth;
}

// 返回生日中的日，格式如下，10
IDCard.prototype.GetBirthDay = function () {
    var BirthDay = '';
    if (this.Valid) BirthDay = this.ID18.substr(12, 2);
    return BirthDay;
}

// 返回性别，1：男，0：女
IDCard.prototype.GetSex = function () {
    var Sex = '';
    if (this.Valid) Sex = this.ID18.charAt(16) % 2;
    return Sex;
}

// 返回15位身份证号码
IDCard.prototype.Get15 = function () {
    var ID15 = '';
    if (this.Valid) ID15 = this.ID15;
    return ID15;
}

// 返回18位身份证号码
IDCard.prototype.Get18 = function () {
    var ID18 = '';
    if (this.Valid) ID18 = this.ID18;
    return ID18;
}

// 返回所在省，例如：上海市、浙江省
IDCard.prototype.GetLocal = function () {
    var Local = '';
    if (this.Valid) Local = this.Local;
    return Local;
}

IDCard.prototype.GetVCode = function (CardNo17) {
    var Wi = new Array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2, 1);
    var Ai = new Array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
    var cardNoSum = 0;
    for (var i = 0; i < CardNo17.length; i++) cardNoSum += CardNo17.charAt(i) * Wi[i];
    var seq = cardNoSum % 11;
    return Ai[seq];
}

IDCard.prototype.CheckValid = function (CardNo18) {
    if (this.GetVCode(CardNo18.substr(0, 17)) != CardNo18.charAt(17)) return false;
    if (!this.IsDate(CardNo18.substr(6, 8))) return false;
    var aCity = {
        11: "北京",
        12: "天津",
        13: "河北",
        14: "山西",
        15: "内蒙古",
        21: "辽宁",
        22: "吉林",
        23: "黑龙江 ",
        31: "上海",
        32: "江苏",
        33: "浙江",
        34: "安徽",
        35: "福建",
        36: "江西",
        37: "山东",
        41: "河南",
        42: "湖北 ",
        43: "湖南",
        44: "广东",
        45: "广西",
        46: "海南",
        50: "重庆",
        51: "四川",
        52: "贵州",
        53: "云南",
        54: "西藏 ",
        61: "陕西",
        62: "甘肃",
        63: "青海",
        64: "宁夏",
        65: "新疆",
        71: "台湾",
        81: "香港",
        82: "澳门",
        91: "国外"
    };
    if (aCity[parseInt(CardNo18.substr(0, 2))] == null) return false;
    this.ID18 = CardNo18;
    this.ID15 = CardNo18.substr(0, 6) + CardNo18.substr(8, 9);
    this.Local = aCity[parseInt(CardNo18.substr(0, 2))];
    return true;
}

IDCard.prototype.IsDate = function (strDate) {
    var r = strDate.match(/^(\d{1,4})(\d{1,2})(\d{1,2})$/);
    if (r == null) return false;
    var d = new Date(r[1], r[2] - 1, r[3]);
    return (d.getFullYear() == r[1] && (d.getMonth() + 1) == r[2] && d.getDate() == r[3]);
}
function enableElementDialogDrag() {
    var el = document;
    let minWidth = 400;
    let minHeight = 300;
    //获取弹框头部（这部分可双击全屏）
    const dialogHeaderEl = el.querySelector('.el-dialog__header');
    //弹窗
    const dragDom = el.querySelector('.el-dialog');
    //给弹窗加上overflow auto；不然缩小时框内的标签可能超出dialog
    dragDom.style.overflow = "auto";
    dialogHeaderEl.onselectstart = new Function("return false");
    //头部加上可拖动cursor
    dialogHeaderEl.style.cursor = 'move';
    // 获取原有属性 ie dom元素.currentStyle 火狐谷歌 window.getComputedStyle(dom元素, null);
    const sty = dragDom.currentStyle || window.getComputedStyle(dragDom, null);
    let moveDown = function (e) {
        // 鼠标按下，计算当前元素距离可视区的距离
        const disX = e.clientX - dialogHeaderEl.offsetLeft;
        const disY = e.clientY - dialogHeaderEl.offsetTop;
        // 获取到的值带px 正则匹配替换
        let styL, styT;
        // 注意在ie中 第一次获取到的值为组件自带50% 移动之后赋值为px
        if (sty.left.includes('%')) {
            styL = +document.body.clientWidth * (+sty.left.replace(/\%/g, '') / 100);
            styT = +document.body.clientHeight * (+sty.top.replace(/\%/g, '') / 100);
        } else {
            styL = +sty.left.replace(/\px/g, '');
            styT = +sty.top.replace(/\px/g, '');
        };
        document.onmousemove = function (e) {
            // 通过事件委托，计算移动的距离
            const l = e.clientX - disX;
            const t = e.clientY - disY;
            // 移动当前元素 
            dragDom.style.left = l + styL + 'px';
            dragDom.style.top = t + styT + 'px';
            //将此时的位置传出去
            //binding.value({x:e.pageX,y:e.pageY})
        };

        document.onmouseup = function (e) {
            document.onmousemove = null;
            document.onmouseup = null;
        };
    }
    dialogHeaderEl.onmousedown = moveDown;
    dragDom.onmousemove = function (e) {
        if (e.clientX > dragDom.offsetLeft + dragDom.clientWidth - 10 || dragDom.offsetLeft + 10 > e.clientX) {
            dragDom.style.cursor = 'w-resize';
        } else if (el.scrollTop + e.clientY > dragDom.offsetTop + dragDom.clientHeight - 10) {
            dragDom.style.cursor = 's-resize';
        } else {
            dragDom.style.cursor = 'default';
            dragDom.onmousedown = null;
        }
        dragDom.onmousedown = function (e) {
            const clientX = e.clientX;
            const clientY = e.clientY;
            let elW = dragDom.clientWidth;
            let elH = dragDom.clientHeight;
            let EloffsetLeft = dragDom.offsetLeft;
            let EloffsetTop = dragDom.offsetTop;
            dragDom.style.userSelect = 'none';
            let ELscrollTop = el.scrollTop;
            //判断点击的位置是不是为头部
            if (clientX > EloffsetLeft && clientX < EloffsetLeft + elW && clientY > EloffsetTop && clientY < EloffsetTop + 100) {
                //如果是头部在此就不做任何动作，以上有绑定dialogHeaderEl.onmousedown = moveDown;
            } else {
                document.onmousemove = function (e) {
                    e.preventDefault(); // 移动时禁用默认事件
                    //左侧鼠标拖拽位置
                    if (clientX > EloffsetLeft && clientX < EloffsetLeft + 10) {
                        //往左拖拽
                        if (clientX > e.clientX) {
                            dragDom.style.width = elW + (clientX - e.clientX) * 1 + 'px';
                        }
                        //往右拖拽
                        if (clientX < e.clientX) {
                            if (dragDom.clientWidth < minWidth) {
                            } else {
                                dragDom.style.width = elW - (e.clientX - clientX) * 1 + 'px';
                            }
                        }
                    }
                    //右侧鼠标拖拽位置
                    if (clientX > EloffsetLeft + elW - 10 && clientX < EloffsetLeft + elW) {
                        //往左拖拽
                        if (clientX > e.clientX) {
                            if (dragDom.clientWidth < minWidth) {
                            } else {
                                dragDom.style.width = elW - (clientX - e.clientX) * 1 + 'px';
                            }
                        }
                        //往右拖拽
                        if (clientX < e.clientX) {
                            dragDom.style.width = elW + (e.clientX - clientX) * 1 + 'px';
                        }
                    }
                    //底部鼠标拖拽位置
                    if (ELscrollTop + clientY > EloffsetTop + elH - 20 && ELscrollTop + clientY < EloffsetTop + elH) {
                        //往上拖拽
                        if (clientY > e.clientY) {
                            if (dragDom.clientHeight < minHeight) {

                            } else {
                                dragDom.style.height = elH - (clientY - e.clientY) * 2 + 'px';
                            }
                        }
                        //往下拖拽
                        if (clientY < e.clientY) {
                            dragDom.style.height = elH + (e.clientY - clientY) * 2 + 'px';
                        }
                    }
                };
                //拉伸结束
                document.onmouseup = function (e) {
                    document.onmousemove = null;
                    document.onmouseup = null;
                };
            }
        }
    }
}

var exitFullscreen = false
//全屏功能
function requestFullScreen(element) {
    if (exitFullscreen) {
        return exitFull();
    }
    exitFullscreen = true
    element = document.documentElement
    // 判断各种浏览器，找到正确的方法
    var requestMethod = element.requestFullScreen || //W3C
        element.webkitRequestFullScreen || //FireFox
        element.mozRequestFullScreen || //Chrome等
        element.msRequestFullScreen; //IE11
    if (requestMethod) {
        requestMethod.call(element);
    } else if (typeof window.ActiveXObject !== "undefined") { //for Internet Explorer
        var wscript = new ActiveXObject("WScript.Shell");
        if (wscript !== null) {
            wscript.SendKeys("{F11}");
        }
    }
}
//退出全屏 判断浏览器种类
function exitFull() {
    exitFullscreen = false
    // 判断各种浏览器，找到正确的方法
    var exitMethod = document.exitFullscreen || //W3C
        document.mozCancelFullScreen || //FireFox
        document.webkitExitFullscreen || //Chrome等
        document.webkitExitFullscreen; //IE11
    if (exitMethod) {
        exitMethod.call(document);
    } else if (typeof window.ActiveXObject !== "undefined") { //for Internet Explorer
        var wscript = new ActiveXObject("WScript.Shell");
        if (wscript !== null) {
            wscript.SendKeys("{F11}");
        }
    }
}
//测试弹框
function testalert(e) {
    app.$message.error(e);
}

function readTextFile(file, callback) {
    var rawFile = new XMLHttpRequest();
    rawFile.overrideMimeType("application/json");
    rawFile.open("GET", file, true);
    rawFile.onreadystatechange = function() {
        if (rawFile.readyState === 4 && rawFile.status == "200") {
            callback(rawFile.responseText);
        }
    }
    rawFile.send(null);
}

/**
 * 将数值保留2位小数返回(不会四舍五入)
 * @param value
 * @returns {string}
 */
getNumber = (value) => {
  const toFixedNum = Number(value).toFixed(3)
  return value ? toFixedNum.substring(0, toFixedNum.toString().length - 1) : '0.00'
}