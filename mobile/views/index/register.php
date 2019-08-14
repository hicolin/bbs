<?php
use yii\helpers\Url;
use common\controllers\PublicController;

?>
<?php $this->beginBlock('header'); ?>
<!-- <head></head>中代码块 -->
<?php $this->endBlock(); ?>
    <link rel="stylesheet" href="<?=Url::base()?>/mobile/web/css/css.css">
    <link rel="stylesheet" type="text/css" href="<?=Url::base()?>/mobile/web/css/basic2.css">
    <link rel="stylesheet" type="text/css" href="<?=Url::base()?>/mobile/web/css/style2.css">
    <link rel="stylesheet" type="text/css" href="<?=Url::base()?>/mobile/web/css/add2.css">
    <link rel="stylesheet" type="text/css" href="css/add.css">
    <link rel="stylesheet" type="text/css" href="http://at.alicdn.com/t/font_395792_7kp2lx6jdag1ra4i.css">
    <script src="js/jquery-3.1.1.min.js"></script>
    <script src="js/mobile/layer.js"></script>
    <style>
        .person_foot{
            display: none;
        }
        .iconfont{
            font-family:"iconfont" !important;
            font-size:16px;
            font-style:normal;
            -webkit-font-smoothing: antialiased;
            -webkit-text-stroke-width: 0.2px;
            -moz-osx-font-smoothing: grayscale;
        }
        .zcc_right .send_code{width: 80px;;height: 28px;border: 1px solid #12cf74;background: #fff;color: #12cf74;border-radius: 4px;}
        body{background-color: #ffffff }
    </style>

<body class="kh_body">
<div class="content_w">
    <div class="head_fixed">
        <p class="head_fixed_p"><a href="javascript:history.go(-1)" class="back"><i class="iconfont icon-xiaoyuhao"></i></a>注册 </p>
    </div>
    <div class="kh_con">
       <div class="zc_con">
           <form method="post" action="<?=Url::toRoute('index/registeradd')?>" onsubmit="return check()">
               <ul>
                   <li>
                       <div class="zcc_left fl">
                           <span>手机号</span>
                       </div>
                       <div class="zcc_mid fl" style="width: 50%">
                           <input type="text" name="tel" class="zc_tel" placeholder="请输入手机号"/>
                       </div>
                       <div class="zcc_right fl">
                           <input type="button" class="send_code" onclick="settime(this)" value='获取验证码'>
                       </div>
                       <div class="clear"></div>
                   </li>
                    <li>
                       <div class="zcc_left fl">
                           <span>验证码</span>
                       </div>
                       <div class="zcc_mid fl">
                           <input type="text" name="code" class="zc_yzm" placeholder="请输入验证码"/>
                       </div>
                       <div class="clear"></div>
                   </li>
                   <li>
                       <div class="zcc_left fl">
                           <span>昵称</span>
                       </div>
                       <div class="zcc_mid fl">
                           <input type="text" name="nickname" class="nickname" placeholder="请输入昵称"/>
                       </div>
                       <div class="clear"></div>
                   </li>

                   <li>
                       <div class="zcc_left fl">
                           <span>登陆密码</span>
                       </div>
                       <div class="zcc_mid fl">
                           <input type="password" name="password" class="zc_psd1" placeholder="请输入密码"/>
                       </div>
                       <div class="clear"></div>
                   </li>
                    <input type="hidden"  class="txt" >
                   <li>
                       <div class="zcc_left fl">
                           <span>确认密码</span>
                       </div>
                       <div class="zcc_mid fl">
                           <input type="password"  name="password2" class="zc_psd2" placeholder="请输入密码"/>
                       </div>
                       <div class="clear"></div>

                   </li>
                   <?php  if(isset($_GET['invitation'])){ ?>
                     <li style="position: absolute;top:-999em">
                         <div class="zcc_left fl" >
                             <span>邀请码</span>
                         </div>
                         <div class="zcc_mid fl">
                             <input type="text"  name="yqm" value="<?=isset($_GET['invitation']) ? $_GET['invitation'] : ''; ?>" class="zc_yqm" placeholder="请输入邀请码" readonly/>
                         </div>
                         <div class="clear"></div>
                     </li>
                  <?php } ?>
               </ul>
               <div class="zc_btn">
                   <button type="submit">确认</button>
               </div>

<!--               <div class="zc_bot">-->
<!--                   <a href="--><?//= Url::toRoute('index/login') ?><!--" style="color: #12cf74;">立即登录</a>-->
<!--               </div>-->

           </form>
       </div>
    </div>
</div>
<script>

  $(".send_code").click(function(){
          var tel=$(".zc_tel").val();
          $.ajax({
            type:'GET',
            url:'<?=Url::toRoute('index/tsms')?>',
            data:{'tel':tel},
            async:false, 
            dataType:'json', 
            success:function(data){
              if(data.telone!=0){
                  $('.txt').val(data.telone);
              }
              if(data.msg==-200){
                layer.open({
                content: '验证码发送成功',
                skin: 'msg',
                time: 15000
                });
              }
            }
        })
    })
  
       var countdown=60;
    function settime(obj) {
        var str=$(".zc_tel").val();
        var reg=/^1(3|4|5|7|8)\d{9}$/;
        if($('.txt').val() > 0){
          layer.open({
                  content: '此号码已经注册'
                  ,skin: 'msg'
                  ,time: 2000
              });
         return  false;
        }else if(!reg.test(str)){
            layer.open({
                content: '请输入正确的手机号'
                ,skin: 'msg'
                ,time: 1500
            });
            return false;
        }else if (countdown == 0){
            obj.removeAttribute("disabled");
            $(".send_code").val("重新获取");
            countdown = 60;
            return;
        } else {
            obj.setAttribute("disabled", true);
            $(".send_code").val("重新发送(" + countdown + ")");
            countdown--;
        }
        setTimeout(function() {
                    settime(obj) }
                ,1000)
    }

    function zc_yzm(){
        var str=$(".zc_yzm").val();
        var reg=/^[\w]{2,20}$/g;
        if(!reg.test(str)){
            layer.open({
                content: '请输入验证码'
                ,skin: 'msg'
                ,time: 1500
            });
            return false;
        }
        else{return true;}
    }

    function nickname(){
        var str=$(".nickname").val();
        var reg=/^[\w]{2,20}$/g;
        /*if(!reg.test(str)){
            layer.open({
                content: '请输入昵称'
                ,skin: 'msg'
                ,time: 1500
            });
            return false;
        }*/
        if(str==''){
          layer.open({
                content: '请输入昵称'
                ,skin: 'msg'
                ,time: 1500
          });
          return false;
        }
        else{return true;}
    }

    function zc_psd1(){
        var str=$(".zc_psd1").val();
        var reg=/^[\S]{2,20}$/g;
        if(!reg.test(str)){
            layer.open({
                content: '请输入密码'
                ,skin: 'msg'
                ,time: 1500
            });
            return false;
        }
        else{return true;}
    }

    function bijiao(){
      var str1=$(".zc_psd1").val();
      var str2=$(".zc_psd2").val();
      if(str1==''&&str2==''){
          layer.open({
                content: '请输入密码'
                ,skin: 'msg'
                ,time: 1500
          });
            return false;

      }
      if(str1!=str2){
          layer.open({
                content: '两次密码不一致'
                ,skin: 'msg'
                ,time: 1500
          });
            return false;
      }else{

          return true;
      }
     

    }
    function zc_yqm(){
        var str=$(".zc_yqm").val();
        var reg=/^[\w]{2,20}$/g;
        if(!reg.test(str)){
            layer.open({
                content: '请输入邀请码'
                ,skin: 'msg'
                ,time: 1500
            });
            return false;
        }
        else{return true;}
    }
  
    function check() {
        if(zc_yzm()&&nickname()&&zc_psd1()&&zc_yqm()&&bijiao()){
            return true;
        }else{
            return false;
        }
    }

    $(function(){
        $('.kh4_checkbox label').click(function(){
            if($(this).hasClass("checked")){
                $(this).removeClass("checked")
            }else{
                $(this).addClass("checked")
            }
        });
    });

</script>
</body>
<?php $this->beginBlock('footer'); ?>
<?php $this->endBlock(); ?>