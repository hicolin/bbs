/**
 * 进度条 Nprogress
 */
function bindNprogress() {
    $(document).ready(function () {
        NProgress.start();
    });
    $(window).load(function () {
        NProgress.done();
    })
}

/**
 * 底部tabBar切换样式
 */
function switchTabBar() {
    var href = getUrlRelativePath(location.href);
    $('.person_foot li a').each(function (index, obj) {
        var src = $(this).attr('href');
        if (src == href) {
            $(this).find('span, i').css({'color': 'rgb(59, 173, 255)'})
        }
    })
}

/**
 * 图片懒加载
 */
$(function () {
    $("img.lazyload").lazyload();
})

/**
 * 添加H5+ API
 */
function addPlusReady() {
    if(window.plus){
        plusReady()
    }else{
        document.addEventListener('plusready',plusReady,false)
    }
}

/**
 * h5+ 保存图片到相册
 * @param src
 */
function saveGalleryPic(src) {
    var dtask = plus.downloader.createDownload( src, {}, function ( d, status ) {
        if ( status == 200 ) {
            plus.gallery.save( d.filename, function(){
                plus.nativeUI.toast('保存成功');
            }, function(){
                console.log('保存失败');
            });
        } else {
            plus.nativeUI.toast( "Download failed: " + status );
        }
    });
    dtask.start();
}

/**
 * 判断是否为iPhone
 * @returns {boolean}
 */
function isIphone() {
    var u = navigator.userAgent;
    if(u.indexOf('iPhone') > -1){
        return true;
    }
    return false;
}

/**
 * 复制文本内容
 * @el 类名(copy-text)
 */
var clipboard = new ClipboardJS('.copy-text');
clipboard.on('success', function(e) {
    layer.msg('复制成功',{icon:1,shift:6,skin:'layui-layer-bai',time:1000});
});
clipboard.on('error', function(e) {
    console.log(e);
});

/**
 * 图片下载
 * @param src
 */
function downloadImage(src) {
    if(isIphone()){
        layer.msg('暂不支持iPhone手机，请长按图片保存或截屏',{icon:7,time:4000})
    }else{
        var $a = $("<a></a>").attr("href", src).attr("download", "poster.png");
        $a[0].click();
    }
}

/**
 * 获取URL的相对路径
 * @returns {string}
 */
function getUrlRelativePath()
{
    var url = document.location.toString();
    var arrUrl = url.split("//");

    var start = arrUrl[1].indexOf("/");
    var relUrl = arrUrl[1].substring(start);//stop省略，截取从start开始到结尾的所有字符

    // if(relUrl.indexOf("?") != -1){
    //     relUrl = relUrl.split("?")[0];
    // }
    return relUrl;
}




