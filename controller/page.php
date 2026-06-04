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
get("/mypage", function () {
  if (ss() && ss()->id === 'admin') views("mypage.admin");
  else if (ss()) views("mypage.basic");
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
get('/review/{idx}', function ($idx) {
  views("review", ["idx" => $idx]);
});
get("/profile/{idx}", function ($idx) {
  views('/profile', ["idx" => $idx]);
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
  $user = ss();
  if (!$user) back("로그인 후 이용할 수 있는 기능입니다");
  if (db::fetch("select * from tours where admin_user = '$user->idx' and isAccept = 1 and isCompleted = 0")) back("이미 운영 중인 탐방이 있습니다");
  if (db::fetch("select * from applys where user_idx = '$user->idx' and status = 1")) back("이미 가입된 탐방이 있습니다");
  db::exec("insert into tours(title, festival, date, max_people, admin_user) values ('$title', '$festival', '$date', '$max_people', '$user->idx')");
  move("/tour", "탐방이 성공적으로 신청되었습니다");
});
post('/addFestival', function () {
  extract($_POST);
  $file = $_FILES["file"];
  $path = '/assets/festivals/' . $file["name"];
  if (isset($file["tmp_name"]) && move_uploaded_file($file["tmp_name"], ".$path")) {
    db::exec("insert into festivals(image, name, start_date, end_date, address) values ('$path', '$name', '$start_date', '$end_date', '$address')");
    move("/mypage", "축제가 성공적으로 추가되었습니다");
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
  db::exec("update tours set isAccept = 1 where idx = '$idx'");
  move("/mypage", "탐방이 수락되었습니다");
});
post("/tourReject", function () {
  extract($_POST);
  db::exec("update tours set isAccept = 0 where idx = '$idx'");
  move("/mypage", "탐방이 거절되었습니다");
});
post("/tourApply", function () {
  extract($_POST);
  $user = ss();
  if (!$user) back("로그인 후 이용할 수 있는 기능입니다");
  $tour = db::fetch("select * from tours where idx = '$tour_idx'");
  if (date("Y-m-d") >= $tour->date) back("이미 진행중입니다");
  if ($tour->admin_user == $user->idx) back("해당 탐방 운영자는 가입 신청이 불가능합니다");
  if (db::fetch("select * from tours where admin_user = '$user->idx' and isAccept = 1 and isCompleted = 0")) back("이미 운영 중인 탐방이 있습니다.");
  if (db::fetch("select * from applys where user_idx = '$user->idx' and status = 0")) back("이미 신청 중인 탐방이 있습니다");
  if (db::fetch("select * from applys where user_idx = '$user->idx' and status = 1")) back("이미 가입된 탐방이 있습니다");
  db::exec("insert into applys(tour_idx, user_idx) values ('$tour_idx', '$user->idx')");
  move("/tour", "탐방에 신청을 완료했습니다");
});
post("/applyAccept", function () {
  extract($_POST);
  $tour = db::fetch("select * from tours where idx = '$tour_idx'");
  db::exec("update applys set status = 1 where user_idx = '$user_idx' and tour_idx = '$tour_idx'");

  $tour_member = db::fetchAll("select * from applys where tour_idx = '$tour_idx' and status = 1");
  if (count($tour_member) + 1 >= $tour->max_people || date("Y-m-d") >= $tour->date) {
    db::exec("delete from applys where tour_idx = '$tour_idx' and status = 0");
  }
  move("/mypage", "탐방 신청을 수락하였습니다");
});
post("/applyReject", function () {
  extract($_POST);
  db::exec("delete from applys where user_idx = '$user_idx' and tour_idx = '$tour_idx'");
  move("/mypage", "탐방 신청을 거절하였습니다");
});
post("/addAdminReview", function () {
  extract($_POST);
  $user = ss();
  db::exec("insert into reviews(rating, content, tour_idx, user_idx, is_admin_review) values ('$rating', '$content', '$tour_idx', '$user->idx', 1)");
  db::exec("update tours set isCompleted = 1 where idx = '$tour_idx'");
  foreach ($member_rating as $target_idx => $target_rating) {
    db::exec("insert into member_ratings(target_user_idx, rating, tour_idx) values ('$target_idx', '$target_rating', '$tour_idx')");
  }
  move("/mypage", "탐방을 완료하였습니다");
});
post('/addReview', function () {
  extract($_POST);
  $user = ss();
  db::exec("insert into reviews(rating, content, tour_idx, user_idx) values ('$rating', '$content', '$tour_idx', '$user->idx')");
  foreach ($member_rating as $target_idx => $target_rating) {
    db::exec("insert into member_ratings(target_user_idx, rating, tour_idx) values ('$target_idx', '$target_rating', '$tour_idx')");
  }
  move("/mypage", "후기가 등록되었습니다");
});

get('/test', function () {
  // '2026-06-2'
  // Y-m-d H:i:s
  $계약해지날 = '2025-12-01 00:00:01';

  $second = 1;
  $minute = $second * 60;
  $hour = $minute * 60;
  $day = $hour * 24;

  $startTime = '2026-06-02 19:00:00';
  $endTime = '2026-06-02 21:00:00';
  $inputStartTime = 16;
  $inputEndTime = 18;

  $otherReservationStartTime = 17;
  $otherReservationEndTime = 19;

  $now = time();

  // $inputStartTime < $otherReservationStartTime && $inputStartTime > $otherReservationEndTime
  // ||
  // $inputStartTime < $otherReservationStartTime && $inputStartTime > $otherReservationEndTime


  // if () {
  //   echo "예약 가능";
  // } else {
  //   echo "꺼저";
  // }

  // date()
  // dateTime()
  // time()
  // strtotime()

  // $삼일후 = $now + $day * 3;

  echo $계약해지날;
  echo '<br/>';
  echo strtotime($계약해지날) + 1;

  echo '<br/>';
  echo time();
});
