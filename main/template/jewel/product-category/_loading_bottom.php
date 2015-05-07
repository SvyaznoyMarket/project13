<style>
    .loader:before,
    .loader:after,
    .loader {
        border-radius: 50%;
        width: 1.5em;
        height: 1.5em;
        -webkit-animation-fill-mode: both;
        animation-fill-mode: both;
        -webkit-animation: load7 1.8s infinite ease-in-out;
        animation: load7 1.8s infinite ease-in-out;
    }
    .loader {
        margin: 1em auto;
        font-size: 10px;
        position: relative;
        text-indent: -9999em;
        -webkit-transform: translateZ(0);
        -ms-transform: translateZ(0);
        transform: translateZ(0);
        -webkit-animation-delay: -0.16s;
        animation-delay: -0.16s;
    }
    .loader:before {
        left: -2.5em;
        -webkit-animation-delay: -0.32s;
        animation-delay: -0.32s;
    }
    .loader:after {
        left: 2.5em;
    }
    .loader:before,
    .loader:after {
        content: '';
        position: absolute;
        top: 0;
    }
    @-webkit-keyframes load7 {
        0%,
        80%,
        100% {
            box-shadow: 0 2.5em 0 -1.3em #999;
        }
        40% {
            box-shadow: 0 2.5em 0 0 #999;
        }
    }
    @keyframes load7 {
        0%,
        80%,
        100% {
            box-shadow: 0 2.5em 0 -1.3em #999;
        }
        40% {
            box-shadow: 0 2.5em 0 0 #999;
        }
    }
</style>
<div id="ajaxgoods" class="bNavLoader hf">
    <div class="loader">Идет загрузка</div>
</div>

