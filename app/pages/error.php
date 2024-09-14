<!DOCTYPE html>
<html dir="ltr" lang="zh">

<head>
    <meta charset="utf-8">
    <meta name="theme-color" content="#fff">
    <meta name="viewport" content="initial-scale=1, minimum-scale=1, width=device-width">
    <title>系统错误</title>
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
            background-image: -webkit-image-set(url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEgAAABICAMAAABiM0N1AAABAlBMVEUAAADcRTfcRDfdRET/gIDcRjr/ZmbjVTncRDfcRTfcRDfdRDzgSTncRDjeSDvcRTjbRDfbRDjeRzvcRjfbRjjcRTjcRTjcRTfdRTfcRDjdRTjcRTjbRDjbRTjbRTjbRTfcRjjdRDrcRjfbRTjZQzfcRDjZRDfZRzbWQzXXRDXXQzbXQzbWQjXYSDvWQjbbRDfOQDPSQTTUQjXCPDDNPzPJPjLGPTHVQjXMPzPRQTTWQjXLPzPDPDHYQzbAOzDTQTXHPTLIPjK8Oi++Oy/FPTHEPTHPQDTQQDTUQTXBPDDKPjK/OzC9Oi/////PQDPRQDS3OS66OS7TQTTEPDHXQjbMPjMBhLaWAAAAL3RSTlMA4tgPAhYFCcL98B4x9ie1+s49WICbqXNKZY3pjuqcgVdLZnL2qKg9zmXpjfontV8LANsAAAJrSURBVHhe7ZTnduIwFAY3ARIgBAg9vW1v173ROylby/u/yso2Fx3MNaxs9h/zAHM+Sfa8+M/s2LFjx+3tdjwH+/sHWxHVAerb8KSyANnUFkRXwLiK78llgJHJxRalwSMd11OGOeV4nsM9FO0dxhJdw4LrOJ6jYy46PoohqgEHatE9JViiFNWTPIElTpIRRXcQ4C6aJ3EJAS4TkUQXsMJFFE++CCsU8xFEBSAoiHsaQNIQ7yuQCFe3DiHUhftKIlzdKoRSFe0r8sXDAkSoumkIigYaIOkIfeWi56EESFm8r1w0fFIl4epWgBA9qOMpmirCfeWijtoa9WSx6taAELFBRl/vilS3BJRIbRk9/VFTsLrifUXRuNfXLU0y/7m6p0CKxqN+v6lJU/k3eJxu7Os5LWKDHi1tYstKG1zON1X3DGiRMR80Mx3fdCbc1+bQe3o2SJrYXcV0fFMxL9xXiz0987BBtux65qaCeF8lHCR3FabBTQ3xvk4M1yN5B/Mw2+urew8hTP1BM38Qnu5evK8gMw+7IcfH9E3ZlEBfMSO//Kf35+Cm6ua+rhbSYDeEa9CUyW3qK1HIjj5DBz8dWd0bWCd6Ult/uMPEr+BmbV/JHrVG/a9MsEybV5fsK50R3frmBFXtCtVXmt73H4PhQ4t9k9rkJ55tYXwZrO4rCEUfPHfUEcuaZC/umw97TfaVpslu2tCb2lRWnBlKFtf+huwrjaa6Pxv7RfgW7nubJPtKI/X0puQO4k/Pfe/ovtLY7KbxVwve0/sE3VeaLosIbkEDvt8Hoq/hKGwQYvoq5OMnoq/hLAbgc/FVn33PX7pAfE5QHR6fAAAAAElFTkSuQmCC) 1x,
                    url(data:image/png;base64,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) 2x);
        }
    </style>
</head>

<body id="body" class="ssl extended-reporting-has-checkbox">
    <div class="interstitial-wrapper">
        <div id="main-content">
            <div class="icon" id="icon"></div>
            <div id="main-message">
                <h1>系统发生错误：</h1>
                <p>虽然不知道错在哪，但是确实出了点小问题...</p>
            </div>
        </div>
        <div class="nav-wrapper">
            <a href="javascript:history.go(-1);"><button id="primary-button">好的，我知道了</button></a>
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