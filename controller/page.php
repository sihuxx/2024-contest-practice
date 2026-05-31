<?php

get("/", function () {
  views("main");
});
get("/map", function () {
  views("map");
});
get("/panorama", function () {
  views("panorama");
});
get("/progress", function () {
  views("progress");
});
get("/sub", function () {
  views("sub");
});
get("/festivals", function () {
  views("festival/festivals");
});
get("/festival/{idx}", function ($idx) {
  views("/festival/festival", ["idx" => $idx]);
});
get("/login", function () {
  views("/auth/login");
});
get("/tour", function() {
  views("tour");
});
get("/register", function () {
  views("/auth/register");
});
post("/signUp", function () {
  extract($_POST);
  $file = $_FILES["file"];
  $path = "/assets/profiles" . $file['name'];
  if (db::fetch("select * from users where id = '$id'")) {
    back("이미 가입된 회원입니다");
  } else {
    if (isset($file["tmp_name"])) {
      if (move_uploaded_file($file["tmp_name"], ".$path")) {
        db::exec("insert into users (id, pw, name, birth, profile) values ('$id', '$pw', '$name', '$birth', '$path')");
        move("/", "회원가입 성공");
      } else {
        back("파일 업로드 실패");
      }
    }
  }
});
post("/signIn", function () {
  extract($_POST);
  $user = db::fetch("select * from users where id = '$id'");
  if ($user) {
    $_SESSION["ss"] = $user;
    move("/", '로그인 성공');
  } else {
    back("아이디 또는 비밀번호가 일치하지 않습니다");
  }
});
get("/logout", function () {
  session_destroy();
  move("/", '로그아웃 성공');
});
