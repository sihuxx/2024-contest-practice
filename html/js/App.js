
const lerp = (a, b, t) => a + t * (b - a);
const dist = (x1, y1, x2, y2) => Math.sqrt((x2 - x1) ** 2 + (y2 - y1) ** 2);

// create a main function that has all three of the classes
// and the lonLatToCanvas needs to be a function
// in order for lonLatToCanvas function to be public
// minLongitude, maxLongitude, longitude, latitude, maxLatitude, minLatitude needs to be public, both canvas
// also maybe change all the longitude and latitudes to be x and y like convertLonLatToCanvas

class Border {
  constructor(map, name) {
    this.map = map;
    this.name = name;

    const xAndY = this.map.flat().map(([lon, lat]) => this.lonLatToCanvas(lon, lat));
    const longitudes = this.map.flat().map(([val]) => val);
    const latitudes = this.map.flat().map(([_, val]) => val);
    const maxLat = Math.max(...latitudes);
    const minLat = Math.min(...latitudes);
    const maxlon = Math.max(...longitudes);
    const minlon = Math.min(...longitudes);
    const [minX, minY] = this.lonLatToCanvas(minlon, minLat);
    const [maxX, maxY] = this.lonLatToCanvas(maxlon, maxLat);
    const width = maxX - minX;
    const height = maxY - minY;
    this.metadata = {
      xAndY,
      longitudes,
      latitudes,
      maxLat,
      minLat,
      maxlon,
      minlon,
      width,
      height,
      minX,
      maxX,
      minY,
      maxY,
    };
  }

  get path() {
    const path = new Path2D();
    this.map.forEach((border) => {
      border.forEach(([longitude, latitude], i) => {
        const [x, y] = this.lonLatToCanvas(longitude, latitude);
        path[i ? 'lineTo' : 'moveTo'](x, y);
      })
      path.closePath();
    })
    return path;
  }

  lonLatToCanvas(lon, lat) {
    const x = ((lon - this.constructor.minlon) / this.constructor.lonRange) * this.constructor.canvas.width;
    const y = ((this.constructor.maxLat - lat) / this.constructor.latRange) * this.constructor.canvas.height;
    return [x, y];
  }

  stroke(ctx) {
    ctx.save();
    ctx.lineWidth = 1;
    ctx.strokeStyle = '#333';
    ctx.stroke(this.path);
    ctx.restore();
  }

  draw(ctx, color) {
    ctx.save();
    this.stroke(ctx);
    ctx.fillStyle = color;
    ctx.fill(this.path);
    ctx.restore();
  }

  showName(ctx) {
    ctx.save();
    ctx.font = 'bold 18px 맑은 고딕';
    ctx.fillStyle = '#333';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.fillText(this.name, this.metadata.minX + this.metadata.width / 2, this.metadata.minY + this.metadata.height / 2);
    ctx.restore();
  }
}

class City extends Border {
  constructor({ name, count, map, stateName }) {
    super(map, name);
    this.count = count;
    this.stateName = stateName;
  }

  showName(ctx) {
    ctx.save();
    ctx.font = 'bold 12px 맑은 고딕';
    ctx.fillStyle = '#333';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    ctx.fillText(this.name, this.metadata.minX + this.metadata.width / 2, this.metadata.minY + this.metadata.height / 2);
    ctx.restore();
  }
}

class State extends Border {
  constructor({ name, count, map, cities }) {
    super(map, name);
    this.count = count;
    this.cities = cities.map(this.createCity.bind(this));
  }

  createCity(options) {
    return new City({ ...options, stateName: this.name });
  }

  strokeCities(ctx) {
    this.cities.forEach((city) => city.stroke(ctx));
  }
}

// might want to change hoveredState and hoveredCity logic so hovering over city would also mean hovered over state but not the other way around
class App {
  get isOpen() {
    return $('.toggle').hasClass('active');
  }
  set isOpen(bool) {
    $('.toggle').toggleClass('active', bool);
    $('aside').css('transform', `translateX(${bool ? 0 : -100}%)`)
  }

  constructor() {
    this.isMouseDown = false;
    this.mousePos = { offsetX: 0, offsetY: 0 };
    this.mouseDownPos = { offsetX: 0, offsetY: 0 };
    this.states = [];
    this.activeStates = [];
    this.activeCities = [];
    this.highlight = { festival: null, obj: null }
    this.shiftKey = false;
    this.panoramaAvaliable = ['2024 웰컴대학로', '2025 카운트다운&해맞이 축제', '계룡산산신제', '장보고수산물축제', '제51회신라문화제'];
    this.init();
  }

  async init() {
    this.festivals = await fetch('./JSON/festivals.json').then(response => response.json()).then((data) => data.map((d, i) => ({ idx: i + 1, ...d })).sort((a, b) => {
      return new Date(b.start_date) - new Date(a.start_date)
    }));

    const types = [...new Set(this.festivals.map(({ type }) => type))];
    $('aside .types').html(types.reduce((html, type) => html += `<button data-type="${type}" class="btn btn-outline-success btn-sm">${type}</button>`, `<button data-type="" class="btn btn-success btn-sm">전체</button>`));
    this.renderFestivals(this.festivals);

    const data = await fetch('./JSON/map.json').then(response => response.json());

    const $canvas = document.getElementById('mapCanvas');
    const $circleCanvas = document.getElementById('circleCanvas');
    const $popup = document.querySelector('.popup');
    const ctx = $canvas.getContext('2d');
    const circleCtx = $circleCanvas.getContext('2d');

    // Find the min and max latitude and longitude
    const longitudes = data.flatMap(({ map }) => map.flat()).map(([val]) => val);
    const latitudes = data.flatMap(({ map }) => map.flat()).map(([_, val]) => val);
    const maxLat = Math.max(...latitudes);
    const minLat = Math.min(...latitudes);
    const maxlon = Math.max(...longitudes);
    const minlon = Math.min(...longitudes);
    const counts = data.map(({ count }) => count);
    const maxCount = Math.max(...counts);
    const latRange = maxLat - minLat;
    const lonRange = maxlon - minlon;

    // Set canvas dimensions
    $canvas.height = latRange * 130;
    $canvas.width = lonRange * 110;
    $circleCanvas.height = latRange * 130;
    $circleCanvas.width = lonRange * 110;

    // Set static properties for Border classes
    Border.canvas = $canvas;
    Border.maxLat = maxLat;
    Border.minLat = minLat;
    Border.maxlon = maxlon;
    Border.minlon = minlon;
    Border.latRange = latRange;
    Border.lonRange = lonRange;

    this.states = data.map((state) => new State(state));
    this.ctx = ctx;
    this.circleCtx = circleCtx;
    this.$canvas = $canvas;
    this.$circleCanvas = $circleCanvas;
    this.$popup = $popup;
    this.maxCount = maxCount;

    this.setEvents();
    this.render();
  }

  get hoveredState() {
    return this.states.filter((state) => !this.activeStates.includes(state)).find(({ path }) => {
      return this.ctx.isPointInPath(path, this.mousePos.offsetX, this.mousePos.offsetY);
    });
  }

  get hoveredCity() {
    return this.activeStates.flatMap(({ cities }) => cities).find(({ path }) => this.ctx.isPointInPath(path, this.mousePos.offsetX, this.mousePos.offsetY));
  }

  render() {
    const animate = (ts) => {
      this.ctx.clearRect(0, 0, this.$canvas.width, this.$canvas.height);
      this.states.forEach((state) => {
        const a = lerp(0.1, 1, state.count / this.maxCount);
        state.draw(this.ctx, `rgba(0, 108, 250, ${a})`);
      });

      this.activeStates.forEach((state) => { state.strokeCities(this.ctx); });
      this.activeCities.forEach((city) => { city.draw(this.ctx, 'green'); });

      if (this.hoveredCity) {
        this.hoveredCity.draw(this.ctx, '#ffc201');
        this.hoveredCity.showName(this.ctx);
        this.showPopup(this.hoveredCity);
      }

      if (this.hoveredState) {
        this.hoveredState.draw(this.ctx, '#ffc201');
        this.hoveredState.showName(this.ctx);
        this.showPopup(this.hoveredState);
      }

      if (!this.hoveredCity && !this.hoveredState) this.$popup.innerHTML = '';

      if (this.updatingState !== this.hoveredState) {
        this.circleCtx.clearRect(0, 0, this.$canvas.width, this.$canvas.height);
        this.updatingState = null;
      }
      if (this.updatingState) {
        const duration = 500;
        const progress = Math.max(0.01, Math.min(1, (ts - this.mouseDownTime) / duration));
        if (progress < 1) this.encircle(this.updatingState, progress);
        else {
          this.activeStates.push(this.updatingState);
          this.updatingState = null;
        }
      }

      if (this.highlight.obj) {
        this.highlight.obj.draw(this.ctx, '#ffc201');
        this.highlight.obj.showName(this.ctx);
        const [x, y] = this.highlight.obj.lonLatToCanvas(this.highlight.festival.lng, this.highlight.festival.lat);
        this.ctx.save();
        this.ctx.fillStyle = 'red';
        this.ctx.beginPath();
        this.ctx.arc(x, y, 5, 0, Math.PI * 2);
        this.ctx.fill();
        this.ctx.restore();
      }
      // Show names at the end so they are not covered
      this.activeCities.forEach((city) => { city.showName(this.ctx); });

      requestAnimationFrame(animate);
    };
    requestAnimationFrame(animate);
  }

  showPopup(borderLike) {
    this.$popup.innerHTML = `<p>${borderLike.name}: ${borderLike.count}</p>`;
    this.$popup.style.left = this.mousePos.offsetX + 'px';
    this.$popup.style.top = this.mousePos.offsetY + 'px';
  }

  encircle(borderLike, progress) {
    const { offsetX, offsetY } = this.mouseDownPos;
    const { metadata: { minX, minY } } = borderLike;
    const dist1 = Math.max(...this.updatingState.metadata.xAndY.map(([x, y]) => dist(offsetX, offsetY, x, y)));
    const dist2 = dist(offsetX, offsetY, minX, minY);

    const radius = (dist1 + dist2) * progress;

    this.circleCtx.save();
    this.circleCtx.clearRect(0, 0, this.$canvas.width, this.$canvas.height);
    this.circleCtx.clip(borderLike.path);
    this.circleCtx.fillStyle = 'red';
    this.circleCtx.globalAlpha = 0.3;
    this.circleCtx.beginPath();
    this.circleCtx.arc(this.mouseDownPos.offsetX, this.mouseDownPos.offsetY, radius, 0, Math.PI * 2);
    this.circleCtx.fill();
    this.circleCtx.restore();
  }

  mousemove({ offsetX, offsetY }) {
    this.mousePos = { offsetX, offsetY };
  }
  mousedown({ offsetX, offsetY }) {
    this.isMouseDown = true;
    this.mouseDownPos = { offsetX, offsetY };
    this.updatingState = this.hoveredState;
    this.mouseDownTime = performance.now();

    if (this.shiftKey && this.hoveredState) {
      this.shiftKey = false;
      this.updatingState = null;
      return alert('꺼저');
    }

    if (this.hoveredCity) {
      const isActive = this.activeCities.includes(this.hoveredCity);
      if (isActive) {
        this.activeCities = this.activeCities.filter((c) => c !== this.hoveredCity);
      } else {
        this.activeCities.push(this.hoveredCity);
      }
      this.renderFestivals(this.filteredFestivals);
    }

    if (this.shiftKey && this.hoveredCity) {
      const hoveredState = this.states.find(({ name }) => name === this.hoveredCity.stateName);
      this.activeStates = this.activeStates.filter((s) => s !== hoveredState);
      this.activeCities = this.activeCities.filter(({ stateName }) => stateName !== hoveredState.name);
      this.renderFestivals(this.filteredFestivals);
      return;
    }
  }
  mouseup() {
    this.isMouseDown = false;
    this.updatingState = null;
  }
  keydown({ key }) {
    if (key === 'Shift') {
      this.shiftKey = true;
    }
  }
  keyup({ key }) {
    if (key === 'Shift') {
      this.shiftKey = false;
    }
  }

  toggleClick() {
    this.isOpen = !this.isOpen;
  }

  renderFestivals(festivals) {
    $('aside .festivals').html(
      festivals.reduce((html, { idx, name, start_date, end_date, phone, address }) => html += `<div data-idx="${idx}" class="festival align-items-start border p-3 flex-col gap-2">
        <p>축제명: ${name}</p>
        <p>축제기간: ${start_date} ~ ${end_date}</p>
        <p>축제장소: ${address}</p>
        <p>연락처: ${phone}</p>
        ${this.panoramaAvaliable.includes(name) ? `<a data-dir="${name}" class="btn btn-primary view360" href="./panorama.html">360 뷰어</a>` : ''}
      </div>`, '')
    );
  }

  get filteredFestivals() {
    const input = $('form')[0].input.value.trim();
    const selectedCities = this.activeCities.map(({ name }) => name);
    const filterType = $('.types .btn-success').data('type');

    const filteredByType = filterType ? this.festivals.filter(({ type }) => type === filterType) : this.festivals;
    const filteredByCity = selectedCities.length ? filteredByType.filter(({ city }) => selectedCities.includes(city)) : filteredByType;
    return filteredByCity
      .filter(({ name, address, phone }) => [name, address, phone].some((str) => str.includes(input)));
  }

  typeBtnClick({ currentTarget }) {
    $('.types button').removeClass('btn-success').addClass('btn-outline-success');
    $(currentTarget).removeClass('btn-outline-success').addClass('btn-success');
    this.renderFestivals(this.filteredFestivals);
  }

  submit(e) {
    e.preventDefault();
    this.renderFestivals(this.filteredFestivals);
  }
  mouseover({ currentTarget }) {
    const idx = +currentTarget.dataset.idx;
    const festival = this.filteredFestivals.find((f) => f.idx === idx);
    const stateObj = this.states.find(({ name }) => name === festival.state);
    this.highlight = this.activeStates.includes(stateObj) ? {
      obj: stateObj.cities.find(({ name }) => name === festival.city),
      festival,
    } : {
      obj: stateObj,
      festival,
    }
  }
  mouseleave() {
    this.highlight = { festival: null, obj: null };
  }
  view360({ currentTarget }) {
    localStorage.setItem('dir', currentTarget.dataset.dir);
    console.log('hello');
  }

  setEvents() {
    $(this.$canvas)
      .on('mousemove', this.mousemove.bind(this))
      .on('mousedown', this.mousedown.bind(this))
      .on('mouseup', this.mouseup.bind(this))
    $(document)
      .on('keyup', this.keyup.bind(this))
      .on('keydown', this.keydown.bind(this))
      .on('click', '.toggle', this.toggleClick.bind(this))
      .on('click', '.types button', this.typeBtnClick.bind(this))
      .on('submit', 'form', this.submit.bind(this))
      .on('mouseover', '.festival', this.mouseover.bind(this))
      .on('mouseleave', '.festival', this.mouseleave.bind(this))
      .on('click', '.view360', this.view360.bind(this));
  }
}

