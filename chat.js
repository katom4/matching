var conn = new WebSocket('ws://localhost:8080');
conn.onopen = function(e) {
    console.log("Connection established!");
};
var onclass = new Boolean(false);
var count=0;
var nickname;
var per=false;
conn.onmessage = function(e) {
  count+=1;//一回目に送られてきたclassidと今のアカウントのclassidを比較している
  //classidはindex.phpで定義している
  if(count==1)
  {
    if(e.data==filename)
    {
      if(e.data=='person')per=true;
    }
    else
    {
      count=-3;
    }
  }
  if(count==2&&classid===e.data&&per!=true)//classidはbase.phpで定義している
  {
      onclass=true;
  }
  if(count==2&&userid==e.data&&per==true)
  {
      count=9;
  }//個人チャットのとき、まず自分に送られてきているかをチェックする。自分の相手が正しいか確かめるために
  //countを9に飛ばして処理をしている
  
  if(count==3)
  {
    if(onclass==true)
    {
      addChat(e.data,'h3');
    }
  }
  if(count==4)
  {
    if(onclass==true)
    {
      addChat(e.data,'p');//nickname
      onclass=false;
    }
    count=0;
    per=false;
  }
  
  if(count==10&&e.data==partnerid)
  {
      count=2;
      onclass=true;
  }//自分の今の相手が送られてきている人と一致するならば表示、しないならばなにもしない
  else if(count==10)
  {
    count=-2;
    per=false;
  }
  //送信されるごとにリダイレクトされるやつ、動作確認済み
  //setTimeout('link()', 0);
};
function link(){
  location.href='/matching';
}
 


function OnButtonClick(){
  
  text = document.getElementById("text").value;//inputのtextの要素を取得
  addChat(text,'h3')
  if(text != "")//textの中身チェック
  {
    console.log(text);
    conn.send(filename);
    if(filename=='person')
    {
      conn.send(partnerid);
      conn.send(userid);
    }
    else
    {
      conn.send(classid);
    }
    conn.send(text);
    conn.send(nickname);
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
