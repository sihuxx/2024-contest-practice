<main class="sign-content">
  <form action="/signUp" method="post"  enctype="multipart/form-data">
    <h1>회원가입</h1>
    <input type="file" name="file" id="file" required>
    <label>아이디: <input type="text" name="id" id="id" required placeholder="아이디를 입력해주세요"></label>
    <label>비밀번호: <input type="password" name="pw" id="pw" required placeholder="비밀번호를 입력해주세요"></label>
    <label>이름: <input type="text" name="name" id="name" required placeholder="이름을 입력해주세요"></label>
    <label>생년월일: <input type="date" name="birth" id="birth" required placeholder="생년월일을 입력해주세요"></label>
    <button>회원가입</button>
  </form>
</main>

<script>
  document.querySelector("form").onsubmit = e => {
  e.preventDefault();

    const ext = document.querySelector("#file").files[0]?.name.split(".").pop().toLowerCase()
    if(ext !== "jpg" && ext !== "jpeg") return alert("프로필 이미지는 jpg 형식만 가능합니다")

    const id = document.querySelector("#id").value
    if(!/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{3,}$/.test(id)) return alert("아이디는 영문+숫자 조합 3자 이상이여야 합니다")
    
    const pw = document.querySelector("#pw").value
    if(!/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[~!@*?])[A-Za-z\d~!@*?]{4,8}$/.test(pw)) return alert("비밀번호 조건이 맞지 않습니다")
    
    const name = document.querySelector("#name").value
    if (!/^[가-힣]+$/.test(name)) return alert("이름은 한글만 가능합니다.");

    e.target.submit();
  }
</script>