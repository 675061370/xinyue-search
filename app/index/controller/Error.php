// <?php
// namespace app\index\controller;

// use app\index\QfShop;
// use think\facade\View;

// class Error extends QfShop
// {
//     public function __call($method, $args)
//     {
//         $error = $this->authorize();
//         if ($error) {
//             return $error;
//         }
//         View::assign('wechat', $this->wechat);
//         if (file_exists(app_path() . "/view/" . strtolower($this->request->controller()) . "/" . $method . ".html")) {
//             if (key_exists('callback', $args)) {
//                 View::assign('callback', $args['callback']);
//             } else {
//                 View::assign('callback', '/admin');
//             }
//             return View::fetch();
//         } else {
//             return 404;
//         }
//     }
//     public static function show($msg = null)
//     {
//         View::assign('msg', $msg);
//         return View::fetch("/error");
//     }
// }
