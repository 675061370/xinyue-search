<!DOCTYPE html>
<html dir="ltr" lang="zh">

<head>
    <meta charset="utf-8">
    <meta name="theme-color" content="#fff">
    <meta name="viewport" content="initial-scale=1, minimum-scale=1, width=device-width">
    <title>系统操作成功</title>
    <style>
        a {
            color: var(--link-color);
        }

        body {
            --background-color: #fff;
            --error-code-color: var(--google-gray-700);
            --google-blue-100: rgb(210, 227, 252);
            --google-blue-300: rgb(138, 180, 248);
            --google-blue-600: rgb(26, 115, 232);
            --google-blue-700: rgb(25, 103, 210);
            --google-gray-100: rgb(241, 243, 244);
            --google-gray-300: rgb(218, 220, 224);
            --google-gray-500: rgb(154, 160, 166);
            --google-gray-50: rgb(248, 249, 250);
            --google-gray-600: rgb(128, 134, 139);
            --google-gray-700: rgb(95, 99, 104);
            --google-gray-800: rgb(60, 64, 67);
            --google-gray-900: rgb(32, 33, 36);
            --heading-color: var(--google-gray-900);
            --primary-button-fill-color-active: var(--google-blue-700);
            --primary-button-fill-color: var(--google-blue-600);
            --primary-button-text-color: #fff;
            --text-color: var(--google-gray-700);
            background: var(--background-color);
            color: var(--text-color);
            word-wrap: break-word;
        }

        html {
            -webkit-text-size-adjust: 100%;
            font-size: 125%;
        }

        .icon {
            background-repeat: no-repeat;
            background-size: 100%;
        }

        @media (prefers-color-scheme: dark) {

            body.captive-portal,
            body.dark-mode-available,
            body.neterror,
            body.supervised-user-block,
            .offline body {
                --background-color: var(--google-gray-900);
                --error-code-color: var(--google-gray-500);
                --heading-color: var(--google-gray-500);
                --link-color: var(--google-blue-300);
                --primary-button-fill-color-active: rgb(129, 162, 208);
                --primary-button-fill-color: var(--google-blue-300);
                --primary-button-text-color: var(--google-gray-900);
                --text-color: var(--google-gray-500);
            }
        }
    </style>
    <style>
        button {
            border: 0;
            border-radius: 4px;
            box-sizing: border-box;
            color: var(--primary-button-text-color);
            cursor: pointer;
            float: right;
            font-size: .875em;
            margin: 0;
            padding: 8px 16px;
            transition: box-shadow 150ms cubic-bezier(0.4, 0, 0.2, 1);
            user-select: none;
        }

        [dir='rtl'] button {
            float: left;
        }

        .ssl button {
            background: var(--primary-button-fill-color);
        }

        button:active {
            background: var(--primary-button-fill-color-active);
            outline: 0;
        }


        h1 {
            color: var(--heading-color);
            font-size: 1.6em;
            font-weight: normal;
            line-height: 1.25em;
            margin-bottom: 16px;
        }

        h2 {
            font-size: 1.2em;
            font-weight: normal;
        }

        .icon {
            height: 72px;
            margin: 0 0 40px;
            width: 72px;
        }

        .interstitial-wrapper {
            box-sizing: border-box;
            font-size: 1em;
            line-height: 1.6em;
            margin: 14vh auto 0;
            max-width: 600px;
            width: 100%;
        }

        #main-message>p {
            display: inline;
        }

        .nav-wrapper {
            margin-top: 51px;
        }

        .nav-wrapper::after {
            clear: both;
            content: '';
            display: table;
            width: 100%;
        }



        @media (max-width: 700px) {
            .interstitial-wrapper {
                padding: 0 10%;
            }
        }

        @media (max-width: 420px) {

            button,
            [dir='rtl'] button{
                float: none;
                font-size: .825em;
                font-weight: 500;
                margin: 0;
                width: 100%;
            }

            button {
                padding: 16px 24px;
            }

            .interstitial-wrapper {
                padding: 0 5%;
            }


            .nav-wrapper {
                margin-top: 30px;
            }
        }

        @media (min-width: 240px) and (max-width: 420px) and (min-height: 401px),
        (min-width: 421px) and (min-height: 240px) and (max-height: 560px) {
            body .nav-wrapper {
                background: var(--background-color);
                bottom: 0;
                box-shadow: 0 -22px 40px var(--background-color);
                left: 0;
                margin: 0 auto;
                max-width: 736px;
                padding-left: 24px;
                padding-right: 24px;
                position: fixed;
                right: 0;
                width: 100%;
                z-index: 2;
            }

            .interstitial-wrapper {
                max-width: 736px;
            }

        }

        @media (max-width: 420px) and (orientation: portrait),
        (max-height: 560px) {
            body {
                margin: 0 auto;
            }

            button,
            [dir='rtl'] button,
            button.small-link {
                font-family: Roboto-Regular, Helvetica;
                font-size: .933em;
                margin: 6px 0;
                transform: translatez(0);
            }

            .nav-wrapper {
                box-sizing: border-box;
                padding-bottom: 8px;
                width: 100%;
            }


            h1 {
                font-size: 1.5em;
                margin-bottom: 8px;
            }

            .icon {
                margin-bottom: 5.69vh;
            }

            .interstitial-wrapper {
                box-sizing: border-box;
                margin: 7vh auto 12px;
                padding: 0 24px;
                position: relative;
            }

            .interstitial-wrapper p {
                font-size: .95em;
                line-height: 1.61em;
                margin-top: 8px;
            }

        }

        @media (min-width: 421px) and (min-height: 500px) and (max-height: 560px) {
            .interstitial-wrapper {
                margin-top: 10vh;
            }
        }

        @media (min-height: 400px) and (orientation:portrait) {
            .interstitial-wrapper {
                margin-bottom: 145px;
            }
        }

        @media (min-height: 299px) {
            .nav-wrapper {
                padding-bottom: 16px;
            }
        }

        @media (min-height: 500px) and (max-height: 650px) and (max-width: 414px) and (orientation: portrait) {
            .interstitial-wrapper {
                margin-top: 7vh;
            }
        }

        @media (min-height: 650px) and (max-width: 414px) and (orientation: portrait) {
            .interstitial-wrapper {
                margin-top: 10vh;
            }
        }


        .ssl .icon {
            background-image: -webkit-image-set(url(data:image/svg+xml,%3Csvg%20width%3D%2224%22%20height%3D%2224%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20%20style%3d%22fill%3agreen%3bstroke%3awhite%3b%22%20d%3D%22M12%2022C6.477%2022%202%2017.523%202%2012S6.477%202%2012%202s10%204.477%2010%2010-4.477%2010-10%2010zm-1.177-7.86l-2.765-2.767L7%2012.431l3.119%203.121a1%201%200%20001.414%200l5.952-5.95-1.062-1.062-5.6%205.6z%22%2F%3E%3C%2Fsvg%3E) 2x);
        }
    </style>
</head>

<body id="body" class="ssl extended-reporting-has-checkbox">
    <div class="interstitial-wrapper">
        <div id="main-content">
            <div class="icon" id="icon"></div>
            <div id="main-message">
                <h1>系统操作成功：</h1>
                <p><?php echo $message ? $message : '虽然不知道你干啥了，但是确实操作成功了...'; ?></p>
            </div>
        </div>
        <div class="nav-wrapper">
            <a href="<?php echo $url;?>"><button id="primary-button">好的，我知道了</button></a>
        </div>
    </div>


    <style>
        html {
            direction: ltr;
        }

        body {
            font-family: system-ui, PingFang SC, STHeiti, sans-serif;
            font-size: 75%;
        }

        button {
            font-family: system-ui, PingFang SC, STHeiti, sans-serif;
        }
    </style>
</body>

</html>