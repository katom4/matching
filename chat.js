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
      makeChatDiv();
      addChat(e.data,'h6');
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
  if(tag=="h6")
  {
    var divParent=document.getElementById('new');
    var div1=document.createElement('div');
    div1.setAttribute("class","row");
    var div2=document.createElement('div');
    div2.setAttribute("class","bg-light border rounded ml-1");
    var content=document.createElement(tag);
    content.setAttribute("class","my-2 mx-2");
    content.innerHTML=text;
    div2.prepend(content);
    div1.prepend(div2);
    divParent.prepend(div1);
    //divParent.appendChild(div1);
  }
  if(tag=="p")
  {
    var divParent=document.getElementById('new');
    var div1=document.createElement('div');
    div1.setAttribute("class","row");
    var content=document.createElement(tag);
    content.setAttribute("class","text-muted small m-0 mt-2 ml-1");
    content.innerHTML=text;
    div1.prepend(content);
    divParent.prepend(div1);
    //divParent.appendChild(div1);
    var n=document.createElement('div');
    n.setAttribute("class","container-fluid mt-2");
    n.setAttribute("id","new");
  }
  /*var chat = document.createElement(tag);
  chat.className= "chatChild";
  chat.innerHTML = text;
  var parent = document.getElementById('chat');//親要素の取得
  parent.prepend(chat);*/
}

function makeChatDiv()
{
  var div=document.createElement('div');
  div.setAttribute("class","container-fluid mt-2");
  div.setAttribute("id","new");
  var pa=document.getElementById('pa');
  pa.prepend(div);
}
