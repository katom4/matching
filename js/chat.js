var conn = new WebSocket('ws://localhost:8080');
conn.onopen = function(e) {
    console.log("Connection established!");
};
var onclass = new Boolean(false);
var count=0;
var nickname;
conn.onmessage = function(e) {
  count+=1;//一回目に送られてきたclassidと今のアカウントのclassidを比較している
  //classidはindex.phpで定義している
  
  if(count==1&&int(classid)===int(e.data))
  {
    　
      onclass=true;
  }
  if(count==2)
  {
    if(onclass==true)
    {
      addChat(e.data,'h3')
      addChat(nickname,'p');//nicknameはindex.phpで定義している
      
      onclass=false;
    }
    count=0;
  }
  
  //送信されるごとにリダイレクトされるやつ、動作確認済み
  //setTimeout('link()', 0);
};
function link(){
  location.href='/matching';
}

function OnButtonClick(){
  text = document.getElementById("text").value;//inputのtextの要素を取得
  if(text != "")//textの中身チェック
  {
    console.log(text);
    conn.send(classid);
    conn.send(text);//同期する
  }
}
function a(){
  document.write("a");
}

function addChat(text,tag)
{
  var chat = document.createElement(tag);
  chat.className= "chatChild";
  chat.innerHTML = text;
  var parent = document.getElementById('chat');//親要素の取得
  parent.prepend(chat);
}
