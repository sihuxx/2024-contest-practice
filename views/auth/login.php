<main class="sign-content">
  <form action="/signIn" method="post">
    <h1>로그인</h1>
    <label>아이디: <input type="text" name="id" id="id" required placeholder="아이디를 입력해주세요"></label>
    <label>비밀번호: <input type="password" name="pw" id="pw" required placeholder="비밀번호를 입력해주세요"></label>
    <button>로그인</button>
  </form>
</main>

<!-- <script>
  async function load() {
    const festivals = await fetch("/JSON/festivals.json").then(res => res.json())
  console.log(festivals.map(item => `INSERT INTO festivals (name, start_date, end_date, phone, state, city, address, lat, lng, type) VALUES ('${item.name.replace(/'/g, "''")}', '${item.start_date}', '${item.end_date}', '${item.phone}', '${item.state}', '${item.city}', '${item.address.replace(/'/g, "''")}', '${item.lat}', '${item.lng}', '${item.type}');`).join("\n"));
  }
  load()
</script> -->