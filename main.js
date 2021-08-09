let optval;
$(function(){
    $('#sel').on("change",function(){
        optval = $(this).val(); //選択したメニューの値
        $.post({
            url: 'http://localhost/matching/index.php',
            data:{
                'opt': optval
            },
            dataType: 'json', //必須。json形式で返すように設定
        }).done(function(data){
           //連想配列のプロパティがそのままjsonオブジェクトのプロパティとなっている
           $("#pos").text(data.position); //取得した集合場所の値を、html上のID値がposの箇所に代入。
           $("#time").text(data.ap_time); //取得した集合時刻の値を、html上のID値がtimeの箇所に代入。
        }).fail(function(XMLHttpRequest, textStatus, errorThrown){
            alert(errorThrown);
        })
    })
})