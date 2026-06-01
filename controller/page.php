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
get("/myPage", function () {
  views("myPage");
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
get("/tour", function () {
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
post("/addTour", function () {
  extract($_POST);
  db::exec("insert into tours(title, festival, date, max_people, people, user_idx) values ('$title', '$festival', '$date', '$max_people', 1, '$user_idx')");
  move("/tour", "탐방이 성공적으로 모집되었습니다");
});
post('/addFestival', function () {
  extract($_POST);
  $file = $_FILES["file"];
  $path = '/assets/festivals/' . $file["name"];
  if (isset($file["tmp_name"]) && move_uploaded_file($file["tmp_name"], ".$path")) {
    db::exec("insert into festivals(image, name, start_date, end_date, address) values ('$path', '$name', '$start_date', '$end_date', '$address')");
    move("/myPage", "축제가 성공적으로 추가되었습니다");
  } else {
    back("축제 추가에 실패하였습니다");
  }
});
post("/tourAccept", function () {
  extract($_POST);
  $tour = db::fetch("select * from tours where idx = '$idx'");
  if (date("Y-m-d") >= $tour->date) {
    db::exec("delete from tours where idx = '$idx'");
    back("이미 날짜가 지난 탐방입니다");
  }
  db::exec("update tours set status = 1 where idx = '$idx'");
  move("/myPage", "탐방이 수락되었습니다");
});
post("/tourReject", function () {
  extract($_POST);
  db::exec("delete from tours where idx = '$idx'");
  move("/myPage", "탐방이 거절되었습니다");
});
post("/tourApply", function () {
  extract($_POST);
  $user = ss();
  $tour = db::fetch("select * from tours where idx = '$tour_idx'");
  if (!$user) back("로그인 후 이용할 수 있는 기능입니다");
  if (db::fetch("select * from recruits where user_idx = '$user_idx' and tour_idx = '$tour_idx'")) back("이미 신청한 탐방입니다");
  if(date("Y-m-d") >= $tour->date) back("이미 진행중입니다");
  db::exec("insert into recruits(tour_idx, user_idx) values ('$tour_idx', '$user_idx')");
  move("/tour", "탐방에 신청을 완료했습니다");
});
